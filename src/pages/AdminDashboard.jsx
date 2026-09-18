### FILE: src/pages/AdminDashboard.jsx
import React, { useEffect, useState } from 'react';

const Header = () => (
  <nav className="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div className="container-fluid">
      <a className="navbar-brand" href="/admin/dashboard">Admin Dashboard</a>
      <div className="collapse navbar-collapse">
        <ul className="navbar-nav ms-auto">
          <li className="nav-item"><a className="nav-link" href="/admin/auto">Gestione Auto</a></li>
          <li className="nav-item"><a className="nav-link" href="/admin/ordini">Ordini</a></li>
          <li className="nav-item"><a className="nav-link" href="/admin/login">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
);

const StatsPanel = ({ stats }) => (
  <div className="row">
    <div className="col-md-4 mb-3">
      <div className="card text-white bg-primary h-100">
        <div className="card-body">
          <h5 className="card-title">Auto in vendita</h5>
          <p className="card-text display-4">{stats.cars}</p>
        </div>
      </div>
    </div>
    <div className="col-md-4 mb-3">
      <div className="card text-white bg-success h-100">
        <div className="card-body">
          <h5 className="card-title">Ordini Totali</h5>
          <p className="card-text display-4">{stats.orders}</p>
        </div>
      </div>
    </div>
    <div className="col-md-4 mb-3">
      <div className="card text-white bg-warning h-100">
        <div className="card-body">
          <h5 className="card-title">Fatturato</h5>
          <p className="card-text display-4">${stats.revenue}</p>
        </div>
      </div>
    </div>
  </div>
);

const Footer = () => (
  <footer className="bg-dark text-white text-center py-3 mt-4">
    © 2026 AutoVendita
  </footer>
);

const AdminDashboard = () => {
  const [stats, setStats] = useState({ cars: 0, orders: 0, revenue: 0 });

  useEffect(() => {
    const fetchStats = async () => {
      const data = {
        cars: 42,
        orders: 128,
        revenue: 987654.32,
      };
      setStats(data);
    };
    fetchStats();
  }, []);

  return (
    <div className="container">
      <Header />
      <h1 className="my-4">Dashboard Amministratore</h1>
      <StatsPanel stats={stats} />
      <Footer />
    </div>
  );
};

export default AdminDashboard;