### FILE: src/pages/Contatti.jsx
import React, { useState } from 'react';

const Header = () => (
  <header className="bg-primary text-white py-3 mb-4">
    <div className="container">
      <h1 className="mb-0">Contatti</h1>
    </div>
  </header>
);

const Footer = () => (
  <footer className="bg-light text-center py-3 mt-5">
    <div className="container">
      <p className="mb-0">&copy; 2026 AutoVendita. Tutti i diritti riservati.</p>
    </div>
  </footer>
);

const ContactForm = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    message: '',
  });
  const [submitted, setSubmitted] = useState(false);
  const [error, setError] = useState('');

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    if (!formData.name || !formData.email || !formData.message) {
      setError('Per favore, compila tutti i campi.');
      return;
    }
    try {
      const response = await fetch('/backend/controllers/ContactController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });
      if (!response.ok) throw new Error('Errore di rete');
      const result = await response.json();
      if (result.success) {
        setSubmitted(true);
        setFormData({ name: '', email: '', message: '' });
      } else {
        setError(result.message || 'Impossibile inviare il messaggio.');
      }
    } catch (err) {
      setError('Si è verificato un errore. Riprova più tardi.');
    }
  };

  if (submitted) {
    return (
      <div className="alert alert-success" role="alert">
        Grazie per averci contattato! Ti risponderemo al più presto.
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="needs-validation" noValidate>
      {error && (
        <div className="alert alert-danger" role="alert">
          {error}
        </div>
      )}
      <div className="mb-3">
        <label htmlFor="name" className="form-label">
          Nome
        </label>
        <input
          type="text"
          className="form-control"
          id="name"
          name="name"
          value={formData.name}
          onChange={handleChange}
          required
        />
      </div>
      <div className="mb-3">
        <label htmlFor="email" className="form-label">
          Email
        </label>
        <input
          type="email"
          className="form-control"
          id="email"
          name="email"
          value={formData.email}
          onChange={handleChange}
          required
        />
      </div>
      <div className="mb-3">
        <label htmlFor="message" className="form-label">
          Messaggio
        </label>
        <textarea
          className="form-control"
          id="message"
          name="message"
          rows="5"
          value={formData.message}
          onChange={handleChange}
          required
        />
      </div>
      <button type="submit" className="btn btn-primary">
        Invia
      </button>
    </form>
  );
};

const Contatti = () => (
  <div>
    <Header />
    <main className="container mb-5">
      <h2 className="mb-4">Contattaci</h2>
      <ContactForm />
    </main>
    <Footer />
  </div>
);

export default Contatti;