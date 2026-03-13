import React from 'react';

const Pagination = ({ paginationConfig }) => {
    return (
        <>
            <div 
                className="pagination-class-name" 
                data-pagination-class-name={JSON.stringify(paginationConfig || {})}
            ></div>
            <nav className="pagination-container">
                <ul className="pagination justify-content-end mt-2">
                    <li className="page-item">
                        <a className="page-link first-page disabled" href="#" tabIndex="-1" aria-label="First" data-page="1" onClick={(e) => e.preventDefault()}>
                            <i className="fas fa-angle-double-left"></i>
                        </a>
                    </li>
                    <li className="page-item">
                        <a className="page-link prev-page disabled" href="#" tabIndex="-1" aria-label="Previous" onClick={(e) => e.preventDefault()}>
                            <i className="fas fa-angle-left"></i>
                        </a>
                    </li>
                    <div className="d-flex list-page">
                        {/* Render các trang ở đây */}
                    </div>
                    <li className="page-item">
                        <a className="page-link next-page disabled" href="#" tabIndex="-1" aria-label="Next" onClick={(e) => e.preventDefault()}>
                            <i className="fas fa-angle-right"></i>
                        </a>
                    </li>
                    <li className="page-item">
                        <a className="page-link last-page disabled" href="#" tabIndex="-1" aria-label="Last" onClick={(e) => e.preventDefault()}>
                            <i className="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </>
    );
};

export default Pagination;
