import React,{ useEffect, useState } from "react";
import {useAuth} from "../../hooks/useAuth"; 
import { toast } from "react-toastify";
import { useFormError } from '../../hooks/useFormError';
import axios from "axios";
import {useNavigate , Link , useParams } from "react-router-dom";

function Completion(props) {
  const { setErrors, renderFieldError } = useFormError();
  const body = document.querySelector('body');
  body.classList.remove('bg-img');
  const navigate = useNavigate();
  const {getTokenData, getLocalStorageUserdata, setLocalStorageUserdata} = useAuth();
  const apiUrl = process.env.REACT_APP_API_URL;

  // const paramsObject = decodeURI(window.location.search)
  // .replace('?', '')
  // .split('&')
  // .map(param => param.split('='))
  // .reduce((values, [ key, value ]) => {
  //   values[ key ] = value
  //   return values

const { token } = useParams();
  const sendPaymentDetails = async () => {
        console.log(getTokenData(),'sdsd34343');

    console.log(getTokenData().access_token,'sdsd');
    try{
      let headers = {
        "Accept": "application/json", 
        'Authorization': 'Bearer ' + getTokenData().access_token,
        'auth-token' : getTokenData().access_token,
      }
      console.log(headers,'headers');
      const response = await axios.post(`${apiUrl}checkout-success`,{token:token}, { headers: headers });
      if(response.data.status){
        let existingUser = getLocalStorageUserdata();
        existingUser.level_type = 2;
        existingUser.credit_limit = response.data.credit_limit;
        setLocalStorageUserdata(existingUser);

        setTimeout(() => {
          navigate("/sellers-form?search")
        }, 2000);
      }
    }catch(error){
      if(error.response) {
        if (error.response.errors) {
            setErrors(error.response.errors);
        }
        if (error.response.error) {
            toast.error(error.response.error, {position: toast.POSITION.TOP_RIGHT});
        }
    }
    }
  }
  
  useEffect(()=>{
    sendPaymentDetails();
  },[]);

    return (
      <div className="payment-bg">
        <div className="card payment-card p-5">
          <div className="d-flex align-items-center justify-content-center mb-3">
            <span className="payment-card__success"><img src="/assets/images/done.svg" className="w-100" />
            </span>
          </div>
          <h1 className="payment-card__msg fw-bold m-0">Payment Complete</h1>
          <h2 className="payment-card__submsg m-0">Thank you for your transfer</h2>
          <div className="payment-card__tags">
              {/* <Link to="/">
                <span className="payment-card__tag">Back to home</span>
              </Link> */}
          </div>
        </div>
      </div>
    )
  }
  
  export default Completion;
  