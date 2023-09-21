import React,{useState} from 'react'
import EditRequest from '../../partials/Modal/EditRequest';
import SentRequest from '../../partials/Modal/SentRequest';

const BuyerCardResult = (props) => {
    console.log('re render 22');
    const {data,index,activeTab,handleLikeClick,handleDisikeClick,handleClickConfirmation,handleClickEditFlag} = props;

  return (
    <div className="col-12 col-lg-6" >
        <div className={"property-critera-block property-section-"+data.id}>
            <div className="critera-card">
                <div className="center-align">
                    <span className="price-img"><img src="/assets/images/price.svg" className="img-fluid" /></span>
                    <p>Buyer </p>
                    {(activeTab =='more_buyers')?
                    <ul className="like-unlike mb-0 list-unstyled">
                        <li>
                            <span className="numb like-span ">{data.totalBuyerLikes}</span>
                            
                            <span className="ico-no ml-min like-btn-disabled">
                            <img src="/assets/images/like.svg" className="img-fluid" /></span>
                        </li>
                        <li>
                            <span className="ico-no mr-min like-btn-disabled"><img src="/assets/images/unlike.svg" className="img-fluid" /></span>
                            <span className="numb text-end unlike-span">{data.totalBuyerUnlikes}</span>
                        </li>
                    </ul>
                    :
                    (data.createdByAdmin ?  
                        <ul className="like-unlike mb-0 list-unstyled">
                            <li>
                                <span className="numb like-span">{data.totalBuyerLikes}</span>
                                <span className="ico-no ml-min" onClick={()=>{handleLikeClick(data.id, index)}}>
                                {/* <span className="ico-no ml-min" onDoubleClick={handleDoubleClick}> */}
                                <img src={(!data.liked) ? "/assets/images/like.svg" : "/assets/images/liked.svg"} className="img-fluid" /></span>
                            </li>
                            <li>
                                <span className="ico-no mr-min" onClick={()=>{handleDisikeClick(data.id, index)}}>
                                    <img src={(!data.disliked) ? "/assets/images/unlike.svg":"/assets/images/unliked.svg"} className="img-fluid" /></span>
                                <span className="numb text-end unlike-span">{data.totalBuyerUnlikes}</span>
                            </li>
                        </ul>:
                     "")
                    }
                </div>
            </div>
            <div className={"property-critera-details unhide-"+index}>
                <ul className="list-unstyled mb-0">
                    <li>
                        <span className="detail-icon"><img src="/assets/images/user-gradient.svg" className="img-fluid" /></span>
                        <span className="name-dealer">{data.name}</span>
                    </li>
                    <li>
                        <span className="detail-icon"><img src="/assets/images/phone-gradient.svg" className="img-fluid" /></span>
                        {(activeTab =='more_buyers')? 
                            <span className="name-dealer">{data.phone}</span>
                            :
                            <a href={'tel:+'+data.phone} className="name-dealer">{data.phone}</a>
                        }
                    </li>
                    <li>
                        <span className="detail-icon"><img src="/assets/images/email.svg" className="img-fluid" /></span>
                        {(activeTab =='more_buyers')? 
                            <span className="name-dealer">{data.email}</span>
                            :
                            <a href={'mailto:'+data.email} className="name-dealer">{data.email}</a>
                        }
                    </li>
                    <li>
                        <span className="detail-icon">
                            <img src="./assets/images/contact-preferance.svg" className="img-fluid" />
                        </span>
                        <span className="name-dealer">{data.contact_preferance}</span>
                    </li>
                </ul>
            </div>
            <div className="cornor-block">
                {
                    (data.createdByAdmin)?
                        (data.redFlagShow) ? <>
                            <div className="red-flag" onClick={()=>{handleClickEditFlag(data.redFlag,data.id)}}>
                                <img src="/assets/images/red-flag.svg" className="img-fluid" />
                            </div>
                        </>:
                        <div className="show-hide-data">
                            <button type="button" className="unhide-btn" onClick={()=>{handleClickConfirmation(data.id,index)}}>
                                <span className="icon-unhide">
                                    <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clipPath="url(#clip0_677_7400)">
                                    <path d="M1.16699 7.99996C1.16699 7.99996 3.83366 2.66663 8.50033 2.66663C13.167 2.66663 15.8337 7.99996 15.8337 7.99996C15.8337 7.99996 13.167 13.3333 8.50033 13.3333C3.83366 13.3333 1.16699 7.99996 1.16699 7.99996Z" stroke="white" strokeLinecap="round" strokeLinejoin="round"></path>
                                    <path d="M8.5 10C9.60457 10 10.5 9.10457 10.5 8C10.5 6.89543 9.60457 6 8.5 6C7.39543 6 6.5 6.89543 6.5 8C6.5 9.10457 7.39543 10 8.5 10Z" stroke="white" strokeLinecap="round" strokeLinejoin="round"></path>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_677_7400">
                                    <rect width="16" height="16" fill="white" transform="translate(0.5)"></rect>
                                    </clipPath>
                                    </defs>
                                    </svg>
                                </span>
                            </button>
                        </div>:''
                }
            </div>
            <div className={data.createdByAdmin ? 'purchase-buyer':'your-buyer' }>{data.createdByAdmin ? 'Premium buyer':'Your buyer' }</div>
        </div>
    </div>
  )
}
export default BuyerCardResult