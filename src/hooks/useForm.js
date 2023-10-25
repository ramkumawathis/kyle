import React, { useState } from "react";
import {useNavigate} from "react-router-dom";

export const useForm = () => {
    let navigate = useNavigate();
    const [errors, setErrors] = useState(null);
    const [message, setMessage] = useState('');

    function renderFieldError(field) {
        // alert(field);
        if(errors && errors.hasOwnProperty(field)) {
            return errors[field][0] ? (
                <span className="invalid-feedback" role="alert">{errors[field][0]}</span>
            ) : null;
        }
        return null;
    }
    
    return {
        navigate,
        errors,
        setErrors,
        message,
        setMessage,
        renderFieldError
    }
}