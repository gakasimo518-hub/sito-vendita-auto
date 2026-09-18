### FILE: src/pages/CatalogoAuto.jsx
import React, { useEffect, useState } from 'react';
import Header from '../components/Header';
import FilterBar from '../components/FilterBar';
import CarList from '../components/CarList';
import Footer from '../components/Footer';

const CatalogoAuto = () => {
  const [cars, setCars] = useState([]);
  const [filters, setFilters] = useState({
    make: '',
    model: '',
    year: '',
    priceMin: '',
    priceMax: '',
  });

  const fetchCars = async () => {
    try {
      const query = new URLSearchParams(filters).toString();
      const response = await fetch(`/api/cars?${query}`);
      const data = await response.json();
      setCars(data);
    } catch (error) {
      console.error('Error fetching cars:', error);
    }
  };

  useEffect(() => {
    fetchCars();
  }, [filters]);

  const handleFilterChange = (newFilters) => {
    setFilters((prev) => ({ ...prev, ...newFilters }));
  };

  return (
    <div className="container-fluid p-0">
      <Header />
      <section className="py-4 bg-light">
        <div className="container">
          <h1 className="mb-4">Catalogo Auto</h1>
          <FilterBar filters={filters} onChange={handleFilterChange} />
          <CarList cars={cars} />
        </div>
      </section>
      <Footer />
    </div>
  );
};

export default CatalogoAuto;