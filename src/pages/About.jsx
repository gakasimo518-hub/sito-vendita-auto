### FILE: src/pages/About.jsx
import React from 'react';
import 'bootstrap/dist/css/bootstrap.min.css';

const Header = () => (
  <header className="bg-primary text-white py-3">
    <div className="container">
      <h1 className="mb-0">AutoVendita</h1>
    </div>
  </header>
);

const Footer = () => (
  <footer className="bg-light text-center py-3 mt-5">
    <div className="container">
      <p className="mb-0">&copy; 2026 AutoVendita. All rights reserved.</p>
    </div>
  </footer>
);

const About = () => (
  <>
    <Header />
    <main className="container my-5">
      <h2>Chi Siamo</h2>
      <p>
        Benvenuti su AutoVendita, la tua concessionaria online di fiducia. Siamo specializzati nella vendita di auto nuove e usate, offrendo un servizio completo dal catalogo alla consegna.
      </p>

      <h3 className="mt-4">La Nostra Missione</h3>
      <p>
        La nostra missione è fornire veicoli di alta qualità a prezzi competitivi, accompagnati da un servizio clienti impeccabile. Ci impegniamo a garantire trasparenza, sicurezza e soddisfazione in ogni transazione.
      </p>

      <h3 className="mt-4">Il Nostro Team</h3>
      <p>
        Il nostro team è composto da professionisti del settore automobilistico con anni di esperienza. Ogni membro è dedicato a offrire consulenza personalizzata, supporto post-vendita e soluzioni su misura per le esigenze dei nostri clienti.
      </p>

      <h3 className="mt-4">Contatti</h3>
      <p>
        Per qualsiasi domanda o richiesta, non esitare a contattarci tramite la pagina <a href="/contatti">Contatti</a>. Siamo sempre disponibili per aiutarti a trovare la tua auto ideale.
      </p>
    </main>
    <Footer />
  </>
);

export default About;