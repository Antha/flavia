<?php $this->extend('/templates/template_main') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<?php $this->section('content') ?>

<style>
    video { height: 300px; width: 100%; max-width: 400px; border: 2px solid #000; border-radius: 10px; }
        canvas { display: none; }
        #scan-area {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%; /* Area scan lebih besar */
            height: 100%;
            border: 3px solid red;
            transform: translate(-50%, -50%);
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
                    <h5 class="font_style_mobile1"><i class="fa-solid fa-user-tie me-2"></i> Welcome, Hendra</h5>
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

                    <p id="results_sn" style="width: 100%; font-size: 16px; font-weight: bold; color: #555; padding: 8px; background: #e9ecef; border-radius: 8px; text-align: center; word-wrap: break-word; overflow-wrap: break-word; max-width: 100%; overflow: hidden;">
                        🔢 Serial Number
                    </p>
                    <p id="results_pn" style="width: 100%; font-size: 16px; font-weight: bold; color: #555; padding: 8px; background: #e9ecef; border-radius: 8px; text-align: center; word-wrap: break-word; overflow-wrap: break-word; max-width: 100%; overflow: hidden;">
                        🔢 Phone Number
                    </p>
                </div>
            </div>
          
        </div>
        <?= $this->include('/includes/include_footer'); ?>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
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

    // $.ajax({
    //     url: '/qris/scrape', // Ganti dengan URL API Anda
    //     method: 'POST',
    //     data: { url: "https://bit.ly/4apPYB5?r=qr" },
    //     success: function (response) {
    //         // Tampilkan hasil ke pengguna
    //         $('#results_sn').html(`<p>Serial Number: ${response.serial_numbers}</p>`);
    //         $('#results_pn').html(`<p>Phone Number: ${response.phone_numbers}</p>`);
    //     },
    //     error: function (xhr) {
    //         const error = xhr.responseJSON ? xhr.responseJSON.error : 'An error occurred';
    //         $('#results_sn').html(`<p>Error: ${error}</p>`);
    //     },
    // });

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

                $.ajax({
                    url: '/qris/scrape', // Ganti dengan URL API Anda
                    method: 'POST',
                    data: { url: qrCode.data },
                    success: function (response) {
                        // Tampilkan hasil ke pengguna
                        $('#results_sn').html(`<p>Serial Number: ${response.serial_numbers}</p>`);
                        $('#results_pn').html(`<p>Phone Number: ${response.phone_numbers}</p>`);
                    },
                    error: function (xhr) {
                        const error = xhr.responseJSON ? xhr.responseJSON.error : 'An error occurred';
                        $('#results_sn').html(`<p>Error: ${error}</p>`);
                    },
                });
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
</script>

<?php $this->endSection() ?>
