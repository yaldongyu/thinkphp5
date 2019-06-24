function all_tel_validate(a) {
    var re = /^1\d{10}$/; //验证手机号
    var val = a;
    if (isNaN(val) || val == null || !re.test(val)) {
        return false;
    } else {
        return true;
    }
}