import React, { useEffect, useState } from 'react';

const Footer = () => {
    const [year, setYear] = useState(new Date().getFullYear());

    return (
        <footer id="page-footer" className="bg-body-light">
            <div className="content py-3">
                <div className="row fs-sm">
                    <div className="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
                        Crafted with <i className="fa fa-heart text-danger"></i> by Nhóm 8 -Thứ 3 Ca 2 <a className="fw-semibold" href="#" target="_blank" rel="noreferrer"></a>
                    </div>
                    <div className="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
                        <a className="fw-semibold" href="#" target="_blank" rel="noreferrer"></a> &copy; {year}
                    </div>
                </div>
            </div>
        </footer>
    );
};

export default Footer;