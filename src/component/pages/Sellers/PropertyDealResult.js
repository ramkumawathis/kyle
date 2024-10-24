import React from 'react';
import { Col, Container, Image, Row } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import Header from "../../partials/Layouts/Header";
import Footer from '../../partials/Layouts/Footer';

const PropertyDealResult = () => {
  return (
    <>
        <Header />
            <section className='main-section position-relative pt-4 pb-120'>
                <Container className='position-relative'>
                    <div className="back-block">
                        <div className="row">
                            <div className="col-4 col-sm-4 col-md-4 col-lg-4">
                                <Link to="/" className="back">
                                    <svg
                                    width="16"
                                    height="12"
                                    viewBox="0 0 16 12"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    >
                                    <path
                                        d="M15 6H1"
                                        stroke="#0A2540"
                                        strokeWidth="1.5"
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                    />
                                    <path
                                        d="M5.9 11L1 6L5.9 1"
                                        stroke="#0A2540"
                                        strokeWidth="1.5"
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                    />
                                    </svg>
                                    Back
                                </Link>
                            </div>
                            <div className="col-7 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                <h6 className="center-head text-center mb-0">
                                    Property Deal Result List
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div className='card-box column_bg_space'>
                        <div className='deal_column'>
                            <Row className='align-items-center'>
                                <Col lg={9}>
                                    <div className='deal_left_column'>
                                        <div className='pro_img'>
                                            <Image src='/assets/images/property-img.png' alt='' />
                                        </div>
                                        <div className='pro_details'>
                                            <h3>real easte company that prioritizes Property</h3>
                                            <ul>
                                                <li>
                                                    <div className='list_icon'>
                                                        <Image src='/assets/images/home_buy.svg' alt='' />
                                                    </div>
                                                    <div className='list_content'>
                                                        <span>23</span>
                                                        <p>Want To Buy</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div className='list_icon'>
                                                        <Image src='/assets/images/home_check.svg' alt='' />
                                                    </div>
                                                    <div className='list_content'>
                                                        <span>23</span>
                                                        <p>Interested</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div className='list_icon'>
                                                        <Image src='/assets/images/home_close.svg' alt='' />
                                                    </div>
                                                    <div className='list_content'>
                                                        <span>23</span>
                                                        <p>Not Interested</p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </Col>
                                <Col lg={3} className='text-center'>
                                    <Link to="#" className='btn btn-fill btn-w-icon'>View Details<svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none">
                                        <path d="M1 6.5L15 6.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10.1 1.5L15 6.5L10.1 11.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </Link>
                                </Col>
                            </Row>
                        </div>
                        <div className='deal_column'>
                            <Row className='align-items-center'>
                                <Col lg={9}>
                                    <div className='deal_left_column'>
                                        <div className='pro_img'>
                                            <Image src='/assets/images/property-img.png' alt='' />
                                        </div>
                                        <div className='pro_details'>
                                            <h3>real easte company that prioritizes Property</h3>
                                            <ul>
                                                <li>
                                                    <div className='list_icon'>
                                                        <Image src='/assets/images/home_buy.svg' alt='' />
                                                    </div>
                                                    <div className='list_content'>
                                                        <span>23</span>
                                                        <p>Want To Buy</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div className='list_icon'>
                                                        <Image src='/assets/images/home_check.svg' alt='' />
                                                    </div>
                                                    <div className='list_content'>
                                                        <span>23</span>
                                                        <p>Interested</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div className='list_icon'>
                                                        <Image src='/assets/images/home_close.svg' alt='' />
                                                    </div>
                                                    <div className='list_content'>
                                                        <span>23</span>
                                                        <p>Not Interested</p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </Col>
                                <Col lg={3} className='text-center'>
                                    <Link to="#" className='btn btn-fill btn-w-icon'>View Details<svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none">
                                        <path d="M1 6.5L15 6.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10.1 1.5L15 6.5L10.1 11.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </Link>
                                </Col>
                            </Row>
                        </div>
                        <div className='deal_column'>
                            <Row className='align-items-center'>
                                <Col lg={9}>
                                    <div className='deal_left_column'>
                                        <div className='pro_img'>
                                            <Image src='/assets/images/property-img.png' alt='' />
                                        </div>
                                        <div className='pro_details'>
                                            <h3>real easte company that prioritizes Property</h3>
                                            <ul>
                                                <li>
                                                    <div className='list_icon'>
                                                        <Image src='/assets/images/home_buy.svg' alt='' />
                                                    </div>
                                                    <div className='list_content'>
                                                        <span>23</span>
                                                        <p>Want To Buy</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div className='list_icon'>
                                                        <Image src='/assets/images/home_check.svg' alt='' />
                                                    </div>
                                                    <div className='list_content'>
                                                        <span>23</span>
                                                        <p>Interested</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div className='list_icon'>
                                                        <Image src='/assets/images/home_close.svg' alt='' />
                                                    </div>
                                                    <div className='list_content'>
                                                        <span>23</span>
                                                        <p>Not Interested</p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </Col>
                                <Col lg={3} className='text-center'>
                                    <Link to="#" className='btn btn-fill btn-w-icon'>View Details<svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none">
                                        <path d="M1 6.5L15 6.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10.1 1.5L15 6.5L10.1 11.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </Link>
                                </Col>
                            </Row>
                        </div>
                    </div>
                </Container>
            </section>
        <Footer />
    </>
  );
};
export default PropertyDealResult;