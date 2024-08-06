import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

const WelcomePage = () => {
    const [name, setName] = useState('');
    const [role, setRole] = useState('');
    const [message, setMessage] = useState('');
    const navigate = useNavigate();

    useEffect(() => {
        // Check if user is logged in by verifying session
        fetch('http://127.0.0.1:5173/session_check.php', {
            method: 'GET',
            credentials: 'include',
        })
            .then(response => response.json())
            .then(data => {
                if (data.logged_in) {
                    setName(data.name);
                    setRole(data.role);
                    if (data.message) {
                        setMessage(data.message);
                    }
                } else {
                    navigate('/login');
                }
            });
    }, [navigate]);

    return (
        <div className="welcome-page">
            <h1>Welcome, {name}!</h1>
            <p>You are logged in as {role}.</p>
            {message && <p style={{ color: 'green' }}>{message}</p>}
            <a href="http://127.0.0.1:5173/logout.php">Logout</a>
        </div>
    );
};

export default WelcomePage;
