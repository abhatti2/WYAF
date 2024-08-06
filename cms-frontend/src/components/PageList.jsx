import React from 'react';
import { Link } from 'react-router-dom';

const PageList = ({ pages }) => (
    <ul className="page-list">
        {pages.map(page => (
            <li key={page.id}>
                <Link to={`/page/${page.id}`}>{page.title}</Link>
            </li>
        ))}
    </ul>
);

export default PageList;
