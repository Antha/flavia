<?php 
namespace App\Validation;

class ExtraRules{
    public function not_space_only(string $str, ?string $fields = null, array $data = []): bool
    {
        return trim($str) !== ''; // Returns false if the string is only spaces
    }
}

?>