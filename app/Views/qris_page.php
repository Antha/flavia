<?php $this->extend('/templates/template_main') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<?php $this->section('content') ?>

<style>
    video { height: 300px; width: 100%; max-width: 400px; border: 2px solid #000; border-radius: 10px; }
        canvas { display: none; }
        #scan-area {
            top: 10px;
            position: absolute;
            top: 25vh;
            left: 0;
            width: 100%;
            height: 55vh;
            border: 3px solid red;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.7);
            z-index: 10;
        }
    #result { font-size: 20px; font-weight: bold; margin-top: 10px; }
</style>

<body class="body-grey">
    <?= $this->include('/includes/loading_spinner'); ?>

    <div id="content">
        <?= $this->include('/includes/include_top_navbar'); ?>

        <div class="home-content mt-md-5 mb-md-5 pt-md-3 mt-4 mb-4 pt-2">
            <div class="container greeting-wrapper">
                <div class="greeting mb-4 text-end">
                    <h5 class="font_style_mobile1"><i class="fa-solid fa-user-tie me-2"></i> Welcome,  <?= esc(session('username')); ?></h5>
                </div>
            </div>

            <div class="container mt-md-4 mb-md-5 mt-3 mb-4">
                <div class="row">
                    <h2>QR Code Scanner (Auto Scan)</h2>
                    <video id="video" autoplay></video>
                    <div id="scan-area"></div>
                      
                    <canvas id="canvas"></canvas>
                    
                    <p id="result" style="font-size: 18px; font-weight: bold; color: #333; padding: 10px; background: #f8f9fa; border-radius: 8px; text-align: center; word-wrap: break-word; overflow-wrap: break-word;">
                        📷 Arahkan kamera ke QR Code (+- 10 - 15cm)
                    </p>

                    <p id="results_sn" style="display:none; width: 100%; font-size: 16px; font-weight: bold; color: #555; padding: 8px; background: #e9ecef; border-radius: 8px; text-align: center; word-wrap: break-word; overflow-wrap: break-word; max-width: 100%; overflow: hidden;">
                        🔢 <i>- Serial Number : [_________________] </i>
                    </p>
                    <p id="results_pn" style="width: 100%; font-size: 16px; font-weight: bold; color: #555; padding: 8px; background: #e9ecef; border-radius: 8px; text-align: center; word-wrap: break-word; overflow-wrap: break-word; max-width: 100%; overflow: hidden;">
                        📞 <i>- Phone Number :  <input type="number" id="results_pn_val" /> </i> <br/>
                        *) <i><span class="text-danger">Please Insert Manualy If QR problem</span></i>
                    </p>

                    <div class="col-3 p-0">
                        <button class="btn btn-danger" id="submit-data">Submit Data</button>
                    </div>
                </div>
            </div>
          
        </div>
        <?= $this->include('/includes/include_footer'); ?>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $(function () {
            $('#security_chckbox').on('change', function () {
                $('#btn_login').prop('disabled', !this.checked).toggleClass('disable-btn', !this.checked);
            });
        }); 
    });
</script>
<script>
    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");
    const context = canvas.getContext("2d");
    const resultText = document.getElementById("result");
    const scanArea = document.getElementById("scan-area");

    let serialNumber, phoneNumber;

    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { width: 1280, height: 720, facingMode: "environment" } 
            });
            video.srcObject = stream;
            requestAnimationFrame(scanQRCode);
        } catch (error) {
            alert("Gagal mengakses kamera: " + error);
        }
    }

    //getMetaData("https://bit.ly/4apPYB5?r=qr");

    function getMetaData(url){
        $.ajax({
            url: '/qris/scrape', // Ganti dengan URL API Anda
            method: 'POST',
            data: { url },
            success: function (response) {
                // Tampilkan hasil ke pengguna
                $('#results_sn').html(`<p>🔢 Serial Number: ${response.serial_numbers}</p>`);
                $('#results_pn_val').val(`${response.phone_numbers}`);

                serialNumber = response.serial_numbers;
                phoneNumber = response.phone_numbers;
            },
            error: function (xhr) {
                const error = xhr.responseJSON ? xhr.responseJSON.error : 'An error occurred';
                $('#results_sn').html(`<p>Error: ${error}</p>`);
            },
        });
    }
  
    function scanQRCode() {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.width = 300;
            canvas.height = 300;

            const scanX = (video.videoWidth - 300) / 2;
            const scanY = (video.videoHeight - 300) / 2;
            context.drawImage(video, scanX, scanY, 300, 300, 0, 0, 300, 300);

        
            // Konversi ke grayscale (memperjelas QR Code kecil)
            const imageData = context.getImageData(0, 0, 300, 300);
            const data = imageData.data;
            for (let i = 0; i < data.length; i += 4) {
                let grayscale = data[i] * 0.3 + data[i + 1] * 0.59 + data[i + 2] * 0.11;
                data[i] = grayscale;
                data[i + 1] = grayscale;
                data[i + 2] = grayscale;
            }
            context.putImageData(imageData, 0, 0);

            const qrCode = jsQR(imageData.data, 300, 300);

            if (qrCode) {
                resultText.innerText = "QR Code: " + qrCode.data;
                resultText.style.color = "green";
                scanArea.style.border = "3px solid green";
                scanArea.style.boxShadow = "0 0 20px rgba(0, 255, 0, 0.7)";
                
                //https://bit.ly/4apPYB5?r=qr
                getMetaData(qrCode.data);
            } else {
                scanArea.style.border = "3px solid red";
                scanArea.style.boxShadow = "0 0 20px rgba(255, 0, 0, 0.7)";
            }
        }
        requestAnimationFrame(scanQRCode);
    }

    window.onload = function () {
        if (typeof jsQR === "undefined") {
            alert("jsQR belum dimuat! Periksa CDN atau unduh secara manual.");
        } else {
            console.log("jsQR berhasil dimuat!");
            startCamera();
        }
    };

    $("#submit-data").click(function(){
        if($("#results_pn_val").val() != ""){
            phoneNumber = $("#results_pn_val").val();
        }

        if( typeof phoneNumber === "undefined"){
            Swal.fire({
                icon: 'warning',
                title: 'Maaf!',
                text: 'MSISDN Are Empty',
                confirmButtonText: 'OK'
            });
            return false;
        }

        const urlParams = new URLSearchParams(window.location.search);
        const cardType = urlParams.get('card_type');

        $.ajax({
            url: '/qris/insert', // Ganti dengan URL API Anda
            method: 'POST',
            data: { msisdn:phoneNumber, cardType},
            beforeSend : function(){
                Swal.fire({
                    title: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading(); // Menampilkan loading animasi bawaan Swal
                    }
                });
            },
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    text: 'Data Have Been Inputed ',
                    confirmButtonText: 'OK'
                });
            },
            error: function (xhr) {
                const error = xhr.responseJSON ? xhr.responseJSON.error : 'An error occurred';
                Swal.fire({
                    icon: 'error',
                    text: error,
                    confirmButtonText: 'OK'
                });
            },
        });
    })
</script>

<?php $this->endSection() ?>
