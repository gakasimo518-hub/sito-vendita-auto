### FILE: src/pages/NotFound.jsx
import React from 'react';
import { useNavigate } from 'react-router-dom';

function NotFound() {
  const navigate = useNavigate();

  return (
    <div className="container text-center mt-5">
      <h1 className="display-4">404 - Pagina non trovata</h1>
      <p className="lead">La pagina che stai cercando non esiste.</p>
      <button className="btn btn-primary" onClick={() => navigate('/')}>
        Vai alla Home
      </button>
    </div>
  );
}

export default NotFound;