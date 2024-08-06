import React from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import Header from './components/Header';
import SearchResults from './pages/SearchResults';
import ViewPage from './pages/ViewPage';
import LoginPage from './pages/LoginPage';
import LogoutPage from './pages/LogoutPage';
import WelcomePage from './pages/WelcomePage';
import './styles/styles.css';

const App = () => (
    <Router>
        <Header />
        <Routes>
            <Route path="/" element={<SearchResults />} />
            <Route path="/page/:id" element={<ViewPage />} />
            <Route path="/login" element={<LoginPage />} />
            <Route path="/register" element={<RegisterPage />} />
            <Route path="/logout" element={<LogoutPage />} />
            <Route path="/welcome" element={<WelcomePage />} />
        </Routes>
    </Router>
);

export default App;
