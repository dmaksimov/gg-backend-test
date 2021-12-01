videoLogin = {
  video: null,
  modal: null,
  loggedIn: false,

  init() {
    this.initModal()
    this.initVideoListener()
    this.listenForEnterKeyUp()
  },

  initModal() {
    this.modal = new bootstrap.Modal(document.getElementById('video-login-modal'), {
      backdrop: 'static',
      keyboard: false,
    })
  },

  initVideoListener() {
    window._wq = window._wq || [];
    _wq.push({
      id: '_all', onReady: (video) => {
        this.video = video

        this.video.bind('timechange', t => {
          if (this.loggedIn) {
            return;
          }

          if (t > 60) {
            this.video.pause()
            this.modal.show()
          }
        })
      }
    });
  },

  doLogin() {
    jQuery('#video-login-modal').find('button').prop('disabled', true).text('Logging in...')

    jQuery.post(videoLoginAjax.ajax_url, {
      _ajax_nonce: videoLoginAjax.nonce,
      action: 'video_login',
      username: jQuery('#video-login-modal').find('input[name="username"]').val(),
      password: jQuery('#video-login-modal').find('input[name="password"]').val()
    }, (response) => {
      if (! response.success) {
        jQuery('#video-login-modal').find('.errors').html(response.data.message)
        jQuery('#video-login-modal').find('button').prop('disabled', false).text('Login')
        return;
      }

      this.loggedIn = true

      jQuery('#video-login-modal').find('button').text('Success!')

      setTimeout(() => {
        this.modal.hide()
        this.video.play()
      }, 1000)
    });
  },

  listenForEnterKeyUp(e) {
    jQuery('#video-login-modal').find('input').keyup(e => {
      let keycode = e.keyCode ? e.keyCode : e.which
      if (keycode === 13) {
        this.doLogin()
      }
    })
  }
}

jQuery(document).ready(function() {
  videoLogin.init()
})
