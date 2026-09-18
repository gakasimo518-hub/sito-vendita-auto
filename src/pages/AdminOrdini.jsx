### FILE: src/pages/AdminOrdini.jsx
import React, { useEffect, useState } from 'react';

const Header = () => (
  <header className="bg-primary text-white py-3 mb-4">
    <div className="container">
      <h1 className="h3 mb-0">Gestione Ordini</h1>
    </div>
  </header>
);

const Footer = () => (
  <footer className="bg-light text-center py-3 mt-4">
    <div className="container">
      <small>&copy; 2026 AutoVendita. Tutti i diritti riservati.</small>
    </div>
  </footer>
);

const OrderTable = ({ orders }) => (
  <table className="table table-striped table-hover">
    <thead className="table-dark">
      <tr>
        <th>ID</th>
        <th>Auto</th>
        <th>Cliente</th>
        <th>Email</th>
        <th>Stato</th>
        <th>Data Ordine</th>
      </tr>
    </thead>
    <tbody>
      {orders.map((order) => (
        <tr key={order.id}>
          <td>{order.id}</td>
          <td>{order.car_make} {order.car_model}</td>
          <td>{order.customer_name}</td>
          <td>{order.customer_email}</td>
          <td>{order.status}</td>
          <td>{new Date(order.order_date).toLocaleDateString()}</td>
        </tr>
      ))}
    </tbody>
  </table>
);

const AdminOrdini = () => {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetch('/api/orders')
      .then((res) => {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
      })
      .then((data) => {
        setOrders(data);
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  }, []);

  return (
    <div className="container">
      <Header />
      <h2 className="mb-4">Ordini dei Clienti</h2>
      {loading && <p>Caricamento degli ordini...</p>}
      {error && <div className="alert alert-danger">Errore: {error}</div>}
      {!loading && !error && (
        <OrderTable orders={orders} />
      )}
      <Footer />
    </div>
  );
};

export default AdminOrdini;