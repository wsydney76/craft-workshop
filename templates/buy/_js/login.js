const app = new Vue({
    el: '#app',
    data: {
        loginName: '{{ loginName }}',
        notice: '',
        error: '',
        sendBtnDisabled: false
    },
    methods: {
        sendMail: function() {
            var data = new FormData();
            data.set('action', 'users/send-password-reset-email');
            data.set('loginName', this.loginName);
            data.set("{{ craft.app.config.general.csrfTokenName|e('js') }}", "{{ craft.app.request.csrfToken|e('js') }}");

            this.sendBtnDisabled = true;

            axios({
                method: 'post',
                url: '',
                headers: {
                    'Accept': 'application/json'
                },
                data: data
            })
                .then(function(response) {
                    // handle success
                    if ("success" in response.data) {
                        app.notice = '{{"email has been sent"|t}}';
                        app.error = '';
                    }
                    if ("error" in response.data) {
                        app.error = '{{"Could not send email"|t}}: ' + response.data.error;
                        app.notice = "";
                    }
                })
                .catch(function(error) {
                    // handle error
                    app.error = error;
                    app.notice = '';
                    console.log(error);
                })
                .then(function() {
                    app.sendBtnDisabled = false;
                });
            ;
        }
    }
});