import React from 'react';
import { Link } from '@inertiajs/react';

const Pagination = ({ links }) => {
    if (!links || links.length === 3) return null; // [prev, number, next] -> nếu chỉ có 1 trang thì 3 links

    return (
        <nav className="pagination-container">
            <ul className="pagination justify-content-end mt-2">
                {links.map((link, index) => {
                    const label = link.label
                        .replace('&laquo; Previous', '')
                        .replace('Next &raquo;', '');
                    
                    const isPrev = link.label.includes('Previous');
                    const isNext = link.label.includes('Next');

                    return (
                        <li key={index} className={`page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}`}>
                            <Link
                                className="page-link"
                                href={link.url || '#'}
                                dangerouslySetInnerHTML={{ 
                                    __html: isPrev ? '<i class="fas fa-angle-left"></i>' : 
                                            isNext ? '<i class="fas fa-angle-right"></i>' : 
                                            label 
                                }}
                                onClick={(e) => !link.url && e.preventDefault()}
                            />
                        </li>
                    );
                })}
            </ul>
        </nav>
    );
};

export default Pagination;
