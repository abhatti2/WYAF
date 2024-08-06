import React from 'react';
import { Link } from 'react-router-dom';

const Header = () => (
    <header className="header">
        <h1>CMS Application</h1>
        <nav>
            <Link to="/">Home</Link>
            <Link to="/login">Login</Link>
            <Link to="/register">Register</Link>
            <Link to="/welcome">Welcome</Link>
            <Link to="/logout">Logout</Link>
        </nav>
    </header>
);

export default Header;
