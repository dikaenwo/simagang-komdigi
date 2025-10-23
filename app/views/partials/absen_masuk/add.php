<?php
$comp_model = new SharedController;
$page_element_id = "add-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
$show_header = $this->show_header;
$view_title = $this->view_title;
$redirect_to = $this->redirect_to;
?>
<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="add"  data-display-type="" data-page-url="<?php print_link($current_page); ?>">
    <?php
    if( $show_header == true ){
    ?>
    <div  class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title">Add New Absen Masuk</h4>
                </div>
            </div>
        </div>
    </div>
    <?php
    }
    ?>
    <div  class="">
        <div class="container">
            <div class="row ">
                <div class="col-md-7 comp-grid">
                    <?php $this :: display_page_errors(); ?>
                    <div  class="bg-light p-3 animated fadeIn page-content">
                        <form id="absen_masuk-add-form" role="form" novalidate enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="<?php print_link("absen_masuk/add?csrf_token=$csrf_token") ?>" method="post">
                            <div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="nama_mahasiswa">Nama Mahasiswa <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-nama_mahasiswa"  value="<?php  echo $this->set_field_value('nama_mahasiswa',USER_NAME); ?>" type="text" placeholder="Enter Nama Mahasiswa"  readonly required="" name="nama_mahasiswa"  class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="bukti_foto">Bukti Foto <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="text-center">
                                                <!-- Video Preview -->
                                                <video id="camera-preview" width="100%" height="auto" autoplay style="max-width: 400px; border: 2px solid #ddd; border-radius: 8px; display: none;"></video>
                                                
                                                <!-- Captured Image Preview -->
                                                <canvas id="canvas" style="display: none;"></canvas>
                                                <img id="photo-preview" src="" style="max-width: 400px; width: 100%; border: 2px solid #ddd; border-radius: 8px; display: none;" />
                                                
                                                <!-- Camera Controls -->
                                                <div class="mt-3">
                                                    <button type="button" id="start-camera" class="btn btn-info">
                                                        <i class="fa fa-camera"></i> Buka Kamera
                                                    </button>
                                                    <button type="button" id="capture-photo" class="btn btn-success" style="display: none;">
                                                        <i class="fa fa-camera"></i> Ambil Foto
                                                    </button>
                                                    <button type="button" id="retake-photo" class="btn btn-warning" style="display: none;">
                                                        <i class="fa fa-redo"></i> Foto Ulang
                                                    </button>
                                                </div>
                                                
                                                <!-- Hidden Input for Base64 Image -->
                                                <input type="hidden" name="bukti_foto" id="ctrl-bukti_foto" required />
                                                <div class="invalid-feedback">Silakan ambil foto terlebih dahulu</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-submit-btn-holder text-center mt-3">
                                <div class="form-ajax-status"></div>
                                <button class="btn btn-primary" type="submit" id="submit-btn" disabled>
                                    Submit
                                    <i class="fa fa-send"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('camera-preview');
    const canvas = document.getElementById('canvas');
    const photoPreview = document.getElementById('photo-preview');
    const startCameraBtn = document.getElementById('start-camera');
    const capturePhotoBtn = document.getElementById('capture-photo');
    const retakePhotoBtn = document.getElementById('retake-photo');
    const buktiInput = document.getElementById('ctrl-bukti_foto');
    const submitBtn = document.getElementById('submit-btn');
    let stream = null;

    // Start Camera
    startCameraBtn.addEventListener('click', async function() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'user', // gunakan 'environment' untuk kamera belakang
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } 
            });
            video.srcObject = stream;
            video.style.display = 'block';
            photoPreview.style.display = 'none';
            startCameraBtn.style.display = 'none';
            capturePhotoBtn.style.display = 'inline-block';
            retakePhotoBtn.style.display = 'none';
        } catch (error) {
            alert('Error mengakses kamera: ' + error.message);
            console.error('Camera error:', error);
        }
    });

    // Capture Photo
    capturePhotoBtn.addEventListener('click', function() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convert to base64
        const imageData = canvas.toDataURL('image/jpeg', 0.8);
        buktiInput.value = imageData;
        
        // Show preview
        photoPreview.src = imageData;
        photoPreview.style.display = 'block';
        video.style.display = 'none';
        
        // Stop camera stream
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        
        // Update buttons
        capturePhotoBtn.style.display = 'none';
        retakePhotoBtn.style.display = 'inline-block';
        submitBtn.disabled = false;
    });

    // Retake Photo
    retakePhotoBtn.addEventListener('click', function() {
        photoPreview.style.display = 'none';
        buktiInput.value = '';
        startCameraBtn.click();
        submitBtn.disabled = true;
    });

    // Form validation
    document.getElementById('absen_masuk-add-form').addEventListener('submit', function(e) {
        if (!buktiInput.value) {
            e.preventDefault();
            alert('Silakan ambil foto terlebih dahulu!');
            return false;
        }
    });
});
</script>

<style>
#camera-preview, #photo-preview {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
.btn {
    margin: 5px;
}
</style>