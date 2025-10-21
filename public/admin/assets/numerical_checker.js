$('document').ready(function() {

    $('.numeric').keyup(function() {
        if (this.value.match(/[^0-9.]/g)) {
            this.value = this.value.replace(/[^0-9.]/g, '');
        }
    });
    $('.numeric_space').keyup(function() {
        if (this.value.match(/[^0-9. ]/g)) {
            this.value = this.value.replace(/[^0-9. ]/g, '');
        }
    });

    $('.numeric_minus').keyup(function() {
        if (this.value.match(/[^0-9.-]/g)) {
            this.value = this.value.replace(/[^0-9.-]/g, '');
        }
    });

    $('.space_checker').keyup(function() {
        if (this.value.match(/[^0-9.-]/g)) {
            this.value = this.value.replace(/ /g,'');
        }
    });
});
