### FILE: src/pages/AdminGestioneAuto.jsx
import React, { useState, useEffect } from 'react';

const Header = () => (
  <nav className="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div className="container-fluid">
      <a className="navbar-brand" href="/admin/dashboard">AutoAdmin</a>
      <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span className="navbar-toggler-icon"></span>
      </button>
      <div className="collapse navbar-collapse" id="navbarNav">
        <ul className="navbar-nav ms-auto">
          <li className="nav-item"><a className="nav-link" href="/admin/auto">Gestione Auto</a></li>
          <li className="nav-item"><a className="nav-link" href="/admin/ordini">Ordini</a></li>
          <li className="nav-item"><a className="nav-link" href="/admin/dashboard">Dashboard</a></li>
        </ul>
      </div>
    </div>
  </nav>
);

const Footer = () => (
  <footer className="bg-dark text-white text-center py-3 mt-4">
    © 2026 AutoVendita. Tutti i diritti riservati.
  </footer>
);

const CarForm = ({ onSubmit, initialData = {}, onCancel }) => {
  const [make, setMake] = useState(initialData.make || '');
  const [model, setModel] = useState(initialData.model || '');
  const [year, setYear] = useState(initialData.year || '');
  const [price, setPrice] = useState(initialData.price || '');
  const [mileage, setMileage] = useState(initialData.mileage || '');
  const [description, setDescription] = useState(initialData.description || '');
  const [image, setImage] = useState(null);

  const handleSubmit = (e) => {
    e.preventDefault();
    const car = { id: initialData.id, make, model, year, price, mileage, description, image };
    onSubmit(car);
    // reset form if adding new
    if (!initialData.id) {
      setMake('');
      setModel('');
      setYear('');
      setPrice('');
      setMileage('');
      setDescription('');
      setImage(null);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="mb-4">
      <div className="row g-3">
        <div className="col-md-4">
          <label className="form-label">Marca</label>
          <input type="text" className="form-control" value={make} onChange={(e) => setMake(e.target.value)} required />
        </div>
        <div className="col-md-4">
          <label className="form-label">Modello</label>
          <input type="text" className="form-control" value={model} onChange={(e) => setModel(e.target.value)} required />
        </div>
        <div className="col-md-4">
          <label className="form-label">Anno</label>
          <input type="number" className="form-control" value={year} onChange={(e) => setYear(e.target.value)} required />
        </div>
        <div className="col-md-4">
          <label className="form-label">Prezzo (€)</label>
          <input type="number" step="0.01" className="form-control" value={price} onChange={(e) => setPrice(e.target.value)} required />
        </div>
        <div className="col-md-4">
          <label className="form-label">Chilometraggio</label>
          <input type="number" className="form-control" value={mileage} onChange={(e) => setMileage(e.target.value)} required />
        </div>
        <div className="col-md-12">
          <label className="form-label">Descrizione</label>
          <textarea className="form-control" rows="3" value={description} onChange={(e) => setDescription(e.target.value)} required></textarea>
        </div>
        <div className="col-md-12">
          <label className="form-label">Immagine</label>
          <input type="file" className="form-control" onChange={(e) => setImage(e.target.files[0])} />
        </div>
      </div>
      <div className="mt-3">
        <button type="submit" className="btn btn-primary me-2">{initialData.id ? 'Aggiorna' : 'Aggiungi'}</button>
        {onCancel && <button type="button" className="btn btn-secondary" onClick={onCancel}>Annulla</button>}
      </div>
    </form>
  );
};

const CarTable = ({ cars, onEdit, onDelete }) => (
  <table className="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Marca</th>
        <th>Modello</th>
        <th>Anno</th>
        <th>Prezzo (€)</th>
        <th>Chilometraggio</th>
        <th>Azioni</th>
      </tr>
    </thead>
    <tbody>
      {cars.map((car) => (
        <tr key={car.id}>
          <td>{car.id}</td>
          <td>{car.make}</td>
          <td>{car.model}</td>
          <td>{car.year}</td>
          <td>{car.price}</td>
          <td>{car.mileage}</td>
          <td>
            <button className="btn btn-sm btn-warning me-1" onClick={() => onEdit(car)}>Modifica</button>
            <button className="btn btn-sm btn-danger" onClick={() => onDelete(car.id)}>Elimina</button>
          </td>
        </tr>
      ))}
    </tbody>
  </table>
);

const AdminGestioneAuto = () => {
  const [cars, setCars] = useState([]);
  const [editingCar, setEditingCar] = useState(null);

  // Simulated fetch from backend
  useEffect(() => {
    const mockCars = [
      { id: 1, make: 'Toyota', model: 'Corolla', year: 2018, price: 15000, mileage: 30000, description: 'Auto in buone condizioni', image: null },
      { id: 2, make: 'Ford', model: 'Fiesta', year: 2016, price: 8000, mileage: 50000, description: 'Economica e maneggevole', image: null },
    ];
    setCars(mockCars);
  }, []);

  const addCar = (car) => {
    const newCar = { ...car, id: cars.length ? Math.max(...cars.map(c => c.id)) + 1 : 1 };
    setCars([...cars, newCar]);
  };

  const updateCar = (updated) => {
    setCars(cars.map(c => (c.id === updated.id ? updated : c)));
    setEditingCar(null);
  };

  const deleteCar = (id) => {
    if (window.confirm('Sei sicuro di voler eliminare questa auto?')) {
      setCars(cars.filter(c => c.id !== id));
    }
  };

  const handleSubmit = (car) => {
    if (car.id) {
      updateCar(car);
    } else {
      addCar(car);
    }
  };

  const cancelEdit = () => setEditingCar(null);

  return (
    <div>
      <Header />
      <div className="container">
        <h2 className="mb-4">Gestione Auto</h2>
        <CarForm onSubmit={handleSubmit} initialData={editingCar} onCancel={editingCar ? cancelEdit : null} />
        <CarTable cars={cars} onEdit={setEditingCar} onDelete={deleteCar} />
      </div>
      <Footer />
    </div>
  );
};

export default AdminGestioneAuto;