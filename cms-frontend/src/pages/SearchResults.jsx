import React, { useState, useEffect } from 'react';
import SearchForm from '../components/SearchForm';
import PageList from '../components/PageList';
import Pagination from '../components/Pagination';

const SearchResults = () => {
    const [pages, setPages] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);

    const handleSearch = (keyword, category) => {
        fetch(`http://127.0.0.1:5173/search.php?keyword=${keyword}&category_id=${category}&page=${currentPage}`)
            .then(response => response.json())
            .then(data => {
                setPages(data.results);
                setTotalPages(data.total_pages);
            });
    };

    useEffect(() => {
        handleSearch('', '');
    }, [currentPage]);

    return (
        <div className="search-results">
            <SearchForm onSearch={handleSearch} />
            <PageList pages={pages} />
            <Pagination
                currentPage={currentPage}
                totalPages={totalPages}
                onPageChange={setCurrentPage}
            />
        </div>
    );
};

export default SearchResults;
