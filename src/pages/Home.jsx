### FILE: src/pages/Home.jsx
import React from 'react';
import Header from '../components/Header';
import Carousel from '../components/Carousel';
import FeaturedCars from '../components/FeaturedCars';
import Footer from '../components/Footer';

const Home = () => {
  return (
    <div>
      <Header />
      <Carousel />
      <section className="container my-5">
        <h2 className="text-center mb-4">Auto in Evidenza</h2>
        <FeaturedCars />
      </section>
      <Footer />
    </div>
  );
};

export default Home;