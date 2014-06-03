M.mod_attcontrol = {}

M.mod_attcontrol.init_manage = function(Y) {

    Y.on('click', function(e) {
        if (e.target.get('checked')) {
            checkall();
        } else {
            checknone();
        }
    }, '#cb_selector' );
};

