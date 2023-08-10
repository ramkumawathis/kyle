import React, { useState } from 'react';

import Layout from './Layout';

import ButtonLoader from '../partials/MiniLoader'
import {useForm} from "../../hooks/useForm";
import axios from 'axios';
import { toast } from 'react-toastify';

function ForgotPassword (){

    const [email, setEmail] = useState('');
    const [loading, setLoading] = useState('');

    const { setErrors, renderFieldError, setMessage, navigate } = useForm();

    const submitForgotPasswordForm = (e) => {
        e.preventDefault();

        setLoading(true);

        setErrors(null);

        setMessage('');

        const apiUrl = process.env.REACT_APP_API_URL;

        let headers = {
            "Accept": "application/json", 
        }

        axios.post(apiUrl+'forgot-password', {email}, {headers: headers}).then(response => {
            setLoading(false);
            if(response.data.status) {
                toast.success(response.data.message, {position: toast.POSITION.TOP_RIGHT});
            }
        }).catch(error => {
            setLoading(false);
            if(error.response) {
                if (error.response.validation_errors) {
                    setErrors(error.response.data.validation_errors);
                }
                if (error.response.errors) {
                    setErrors(error.response.errors);
                }
                if (error.response.error) {
                    toast.error(error.response.error, {position: toast.POSITION.TOP_RIGHT});
                }
            }
        });

    };

    return (
        <Layout>
            <div className="account-in">
                <div className="center-content">
                    <img src="./assets/images/logo.svg" className="img-fluid" alt="" />
                    <h2>Forgot Password</h2>
                </div>
                <form method='post' onSubmit={submitForgotPasswordForm}>
                    <div className="row">
                        <div className="col-12 col-lg-12">
                            <div className="form-group">
                                <label htmlFor="email" >Email</label>
                                <div className="form-group-inner">
                                    <span className="form-icon"><img src="./assets/images/mail.svg" className="img-fluid" alt="mail-icon" /></span>
                                    <input 
                                        type="email" 
                                        id="email"
                                        name="email" 
                                        className="form-control"                                         
                                        placeholder="Enter Your Email" 
                                        value={email} 
                                        onChange={e => setEmail(e.target.value)} 
                                        required autoComplete="email" autoFocus
                                        disabled={ loading ? 'disabled' : ''}
                                    />
                                </div>
                                {renderFieldError('email') }
                            </div>
                        </div>
                        <div className="col-12 col-lg-12">
                            <div className="form-group-btn mb-0">
                                <button type="submit" className="btn btn-fill" disabled={ loading ? 'disabled' : ''}> Submit { loading ? <ButtonLoader/> : ''} </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </Layout>
    )
}
  
export default ForgotPassword;