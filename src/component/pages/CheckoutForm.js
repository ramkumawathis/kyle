import { useState } from "react";
import { useStripe, useElements,AddressElement, PaymentElement, LinkAuthenticationElement} from "@stripe/react-stripe-js";
import {useNavigate } from "react-router-dom";
import { toast } from "react-toastify";

export default function CheckoutForm({setClientSecret}) {
  const stripe = useStripe();
  const elements = useElements();
  const navigate = useNavigate();

  const [address, setAddress] = useState({
    addressLine1: "",
    addressLine2: "",
    city: "",
    state: "",
    country: "",
    zip: "",
  });

  const handleAddressChange = (event) => {
      setAddress({
        ...address,
        [event.target.name]: event.target.value,
      });
  };

  const [message, setMessage] = useState(null);
  const [isProcessing, setIsProcessing] = useState(false);
  const apiUrl = process.env.REACT_APP_API_URL;

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!stripe || !elements) {
      // Stripe.js has not yet loaded.
      // Make sure to disable form submission until Stripe.js has loaded.
      return;
    }

    setIsProcessing(true);
    const baseUrl = window.location.origin;
    console.log(`${baseUrl}/completion`,'ssss');
    const { error, payment  } = await stripe.confirmPayment({
      elements,
      confirmParams: {
        // Make sure to change this to your payment completion page
        return_url: `${baseUrl}/completion`,
      },
    });
    console.log(error,'ss');
    if (!error) {
      // The payment was successful.
      console.log(`Payment successful! Amount: ${payment}`);
    } 
    if (error.type === "card_error" || error.type === "validation_error") {
      setMessage(error.message);
    } else if(error.type === 'card_error' || error.type === 'invalid_request_error'){
      //navigate('/choose-your-plan');
      toast.error("Sorry! Your Payment is not Completed Try Again", {position: toast.POSITION.TOP_RIGHT});
      window.location.reload();
    }else {
      console.log(error);
      setMessage("An unexpected error occured.");
    }

    setIsProcessing(false);
  };

  return (
    <form id="payment-form" onSubmit={handleSubmit}>
        <PaymentElement id="payment-element" cancelUrl={`${window.location.origin}/cancel`}/>
        {/* <LinkAuthenticationElement/> */}
        {/* <AddressElement options={{
          mode: 'shipping',
          allowedCountries: ['IN'],
          blockPoBox: true,
            fields: {
              phone: 'always',
              email: 'always',
            },
            validation: {
              phone: {
                required: 'never',
              },
            },
        }} /> */}
      <button disabled={isProcessing || !stripe || !elements} id="submit">
        <span id="button-text">
          {isProcessing ? "Processing ... " : "Pay now"}
        </span>
      </button>
      {/* Show any error or success messages */}
      {message && <div id="payment-message">{message}</div>}
    </form>
  );
}