import './bootstrap';

import React, {useState} from 'react';
import ReactDOM from 'react-dom/client';
import 'bootstrap/dist/css/bootstrap.min.css';
import Welcome from './components/welcome.jsx';
import Login from "./components/login.jsx";
import Home from "./components/home.jsx"

const App = () => {
    const [page, setPage] = useState('welcome');
    const [token, setToken] = useState(() => {
        return localStorage.getItem('token') || null;
    });

    const getToken = () => {
        return token;
    };

    const saveToken = (newToken) => {
        setToken(newToken);
        localStorage.setItem('token', newToken);
    };


    return (
        <div className={"container-sm mt-5"} style={{
            minHeight: '80vh',
            borderLeft: '1px solid #CCCCCC',
            borderRight: '1px solid #CCCCCC'
        }}>
            {page === 'welcome' && <Welcome setPage={setPage} getToken={getToken} /> }
            {page === 'login' && <Login setPage={setPage} setToken={saveToken} getToken={getToken} />}
            {page === 'home' && <Home setPage={setPage} getToken={getToken} />}
        </div>
    );
};

ReactDOM.createRoot(document.getElementById('react-app')).render(
    <React.StrictMode>
        <App />
    </React.StrictMode>
);
