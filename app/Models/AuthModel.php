<?php  

namespace App\Models;

use CodeIgniter\Model;

use function PHPUnit\Framework\isNull;

class AuthModel extends Model{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect('default');
    }

    public function isDataExists($username,$password){
        $sql = "SELECT * FROM users WHERE username = ".$this->db->escape($username)." LIMIT 1";

        $query = $this->db->query($sql);
        
        if($query){
            return $query->getNumRows();
        }else{
            return $this->db->error();
        }
    }

    public function isDataAuthentic($username,$password){
        $sql = "SELECT * FROM users WHERE username = ".$this->db->escape($username)." AND password  = ".$this->db->escape($password)." LIMIT 1";

        $query = $this->db->query($sql);
        
        if($query){
            return $query->getNumRows();
        }else{
            return $this->db->error();
        }
    }

    function getUserData($username,$password){
        $sql = "SELECT * FROM users WHERE username = ".$this->db->escape($username)." AND password  = ".$this->db->escape($password)." LIMIT 1";

        $query = $this->db->query($sql);
        
        if($query){
            return $query->getResultArray();
        }else{
            return $this->db->error();
        }
    }

   
    public function cekLoginIbra($username_login,$password_login){
        $sql = "SELECT * FROM AIRBUS.tb_login WHERE login_email = ".$this->db->escape($username_login)." AND login_password  = ".$this->db->escape($password_login)." LIMIT 1";
        $query = $this->db->query($sql);
        if($query){
            return $query->getNumRows();
        }else{
            return $this->db->error();
        }
    }

    public function cekUsernameLoginRegisWna($username_login,$password_login){
        $sql = "SELECT * FROM AIRBUS.bot_sikaren_admin_approve 
                WHERE email = ".$this->db->escape($username_login)." AND pwd  = ".$this->db->escape($password_login)."";
        
        $query = $this->db->query($sql);
        if($query){
            return $query->getResultArray();
        }else{
            return $this->db->error();
        }
    }

    public function cekExistsLoginUserName($username_login){
        $sql = "SELECT login_email FROM AIRBUS.tb_login WHERE login_email = ".$this->db->escape($username_login)." LIMIT 1";
        $query = $this->db->query($sql);

        if($query){
            return $query->getNumRows();
        }else{
            return $this->db->error();
        }
    }

    function cekExsitsLoginPassword($username_login){
        $builder = $this->db->table('tb_login');
        $query = $builder->select('login_password,login_email')->getWhere(['login_email' => $username_login],1)->getResult();

        foreach($query as $rows){
            $username = $rows->login_email;
            $pass = $rows->login_password;
        }

        $result = [
                'username' => $username,
                'password' => $pass
        ];

       return $result;
    }

    function updatePassword($username_login,$password_login){
        $builder = $this->db->table('tb_login');
        
        $data = [
            'login_password' => $password_login
        ];

        $builder->where('login_email',$username_login);
        $builder->update($data);
    }
}
?>