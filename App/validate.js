
function validateForm(form) {
    fail = validateUsername(form.username.value)
    fail += validateEmail(form.email.value)
    
    if (fail === "") return true
    else { alert(fail); return false }
}

function validateMalwareForm(form){
    fail = validateMalware(form.Name.value)
    
    if (fail === "") return true
    else { alert(fail); return false }
}

function validateMalware(value){
    regex = /^[a-zA-Z0-9]+$/

    if(!regex.test(value)){
        return "Malware name must contain only alphanumeric values\n"
    }
    return ""

}

function validateUsername(value){
    regex = /^[a-zA-Z0-9_-]+$/

    if(!regex.test(value)){
        return "Username must contain only: english characters,  digits, and -_\n"
    }
    return ""
}


function validateEmail(value){
    regex = /^[a-zA-Z0-9.]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/

    if(!regex.test(value)){
        return "Invalid email format\n"
    }
    return ""

}
