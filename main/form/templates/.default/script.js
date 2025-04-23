BX.ready(function () {
    BX.namespace("Main.Form.Template")
    BX.Main.Form.Template = {
        init: function () {
            let  form = document.querySelector('form[name="feedback_form"]')
            if(form) {
                form.addEventListener("submit", async (event) => {
                    BX.Main.Form.Template.addForm(form)
                    event.preventDefault();
                });
            }
        },

        addForm: function (form) {
            let formData = new FormData(form);
            let formObject = Object.fromEntries(formData.entries());

            BX.ajax.runComponentAction(
                'main:form',
                'addForm', {
                    mode: 'class',
                    data: {form: formObject},
                })
                .then((response) => {
                    if (response.status === 'success') {
                        alert(response.data.msg);
                    }else{
                        alert(response.errors[0].message);
                    }
                })
                .catch((response) => {
                    alert(response.errors[0].message);
                });

        }
    }

    BX.Main.Form.Template.init();
});
