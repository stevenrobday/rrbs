<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="profileModalLabel">Edit profile</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="imgForm" class="row align-items-center justify-content-center">
                        <?= csrf_field() ?>
                        <div class="col-12 text-center">
                            <div class="border align-middle" id="uploadPrompt" style="padding: 50px">
                                Upload an Image
                            </div>
                            <div id="croppie" style="display: none"></div>
                        </div>
                    </form>
                    <div class="row align-items-center justify-content-center mt-3">
                        <div class="col-6 text-center">
                            <input type="file" name="file" id="croppieFile" accept="image/*" style="display: none"/>
                            <button id="browseImg" class="btn btn-primary">Browse</button>
                        </div>
                        <div class="col-6 text-center">
                            <button id="saveImg" class="btn btn-success" style="display: none">Save</button>
                        </div>
                    </div>
                    <div class="row align-items-center justify-content-center">
                        <div class="col-12 text-center">
                            <div id="uploadErrorMsg" class="text-danger"></div>
                            <div id="uploadSuccessMsg" class="text-success"></div>
                        </div>
                    </div>
                    <form id="aboutForm" class="row align-items-center justify-content-center mt-3">
                        <?= csrf_field() ?>
                        <div class="col-12 text-center">
                            <div class="form-floating">
                                <textarea class="form-control" maxlength="1000" placeholder="Write about yourself here" id="aboutTextarea"></textarea>
                                <label for="aboutTextarea">About</label>
                            </div>
                        </div>
                    </form>
                    <div class="row align-items-center justify-content-center">
                        <div class="col-12 text-center">
                            <div class="text-center">
                                <span id="charCount">0</span><span>/1000 characters</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center justify-content-center mt-3">
                        <div class="col-6 text-center">
                            <button id="saveAbout" class="btn btn-success">Save</button>
                        </div>
                    </div>
                    <div class="row align-items-center justify-content-center">
                        <div class="col-12 text-center">
                            <div id="aboutErrorMsg" class="text-danger"></div>
                            <div id="aboutSuccessMsg" class="text-success"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>