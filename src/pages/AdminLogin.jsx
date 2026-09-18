### FILE: src/pages/AdminLogin.jsx
import React, { useState } from 'react';
import { Link } from 'react-router-dom';

const Header = () => (
  <header className="bg-primary text-white py-3 mb-4">
    <div className="container">
      <h1 className="h3 mb-0">Admin Login</h1>
    </div>
  </header>
);

const Footer = () => (
  <footer className="bg-light text-center py-3 mt-4">
    <div className="container">
      <small>&copy; 2026 AutoVendita. All rights reserved.</small>
    </div>
  </footer>
);

const AdminLogin = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await fetch('/backend/controllers/AuthController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      });
      const result = await response.json();
      if (response.ok && result.success) {
        window.location.href = '/admin/dashboard';
      } else {
        setError(result.message || 'Login failed');
      }
    } catch (err) {
      setError('An error occurred while logging in.');
    }
  };

  return (
    <div className="min-vh-100 d-flex flex-column">
      <Header />
      <main className="flex-fill">
        <div className="container">
          <div className="row justify-content-center">
            <div className="col-md-6">
              <div className="card shadow-sm">
                <div className="card-body">
                  <h2 className="card-title mb-4 text-center">Login Admin</h2>
                  {error && (
                    <div className="alert alert-danger" role="alert">
                      {error}
                    </div>
                  )}
                  <form onSubmit={handleSubmit}>
                    <div className="mb-3">
                      <label htmlFor="email" className="form-label">
                        Email address
                      </label>
                      <input
                        type="email"
                        className="form-control"
                        id="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                      />
                    </div>
                    <div className="mb-3">
                      <label htmlFor="password" className="form-label">
                        Password
                      </label>
                      <input
                        type="password"
                        className="form-control"
                        id="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                      />
                    </div>
                    <button type="submit" className="btn btn-primary w-100">
                      Login
                    </button>
                  </form>
                  <div className="mt-3 text-center">
                    <Link to="/admin/dashboard">Back to Dashboard</Link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default AdminLogin;