import React from 'react';

const Pagination = ({ currentPage, totalPages, onPageChange }) => {
    const pages = [...Array(totalPages).keys()].map(num => num + 1);

    return (
        <div className="pagination">
            {currentPage > 1 && (
                <button onClick={() => onPageChange(currentPage - 1)}>Previous</button>
            )}
            {pages.map(page => (
                <button
                    key={page}
                    className={currentPage === page ? 'active' : ''}
                    onClick={() => onPageChange(page)}
                >
                    {page}
                </button>
            ))}
            {currentPage < totalPages && (
                <button onClick={() => onPageChange(currentPage + 1)}>Next</button>
            )}
        </div>
    );
};

export default Pagination;
