<div class="modal fade" id="video-login-modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p>Please login in order to continue watching the video.</p>

                <form>
                    <div class="errors text-danger"></div>

                    <div class="">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username">
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password">
                    </div>

                    <button class="btn btn-primary" type="button" onclick="videoLogin.doLogin()">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>
