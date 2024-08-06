import React, { useState, useEffect } from 'react';

const SearchForm = ({ onSearch }) => {
    const [keyword, setKeyword] = useState('');
    const [categories, setCategories] = useState([]);
    const [category, setCategory] = useState('');

    useEffect(() => {
        fetch('http://127.0.0.1:5173/categories.php')
            .then(response => response.json())
            .then(data => setCategories(data));
    }, []);

    const handleSubmit = (e) => {
        e.preventDefault();
        onSearch(keyword, category);
    };

    return (
        <form onSubmit={handleSubmit} className="search-form">
            <input
                type="text"
                placeholder="Search..."
                value={keyword}
                onChange={(e) => setKeyword(e.target.value)}
            />
            <select value={category} onChange={(e) => setCategory(e.target.value)}>
                <option value="">All Categories</option>
                {categories.map((cat) => (
                    <option key={cat.id} value={cat.id}>
                        {cat.name}
                    </option>
                ))}
            </select>
            <button type="submit">Search</button>
        </form>
    );
};

export default SearchForm;
