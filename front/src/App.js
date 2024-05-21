import './App.css';
import React from "react";
import ReactDom from "react-dom";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import NavBar from "./components/Navbar";
import Header from './components/Header';
import ListarPublis from './pages/publicaciones/ListarPublis';
import ListarCentro from './pages/centros/ListarCentro';
import AgregarCategoria from './pages/categorias/AgregarCategoria';
import Dashboard from "./pages/Dashboard";
import Registrarse from './pages/sesion/Registrarse';
import IniciarSesion from './pages/sesion/IniciarSesion';
import AgregarPublicacion from './pages/publicaciones/CargarPublicacion';
import AgregarCentro from './pages/centros/AgregarCentro';
import MisPublis from './pages/publicaciones/MisPublicaciones';

function App() {
  return (
    <div className='body'>
      <BrowserRouter>
        <div>
          <div className='contenedor'>
            <Header />
            <NavBar className= 'navbar-nav' />
          </div>
          <div  className='contenido'>
            <div className='publicaciones'>
              <Routes>
                <Route path={"/"} element={<Dashboard />} />
                <Route path={"/MisPublicaciones"} element={<MisPublis />} />

                <Route path={"/Registrarse"} element={<Registrarse />} />
                <Route path={"/IniciarSesion"} element={<IniciarSesion />} />

                <Route path={"/agregarPublicacion"} element={<AgregarPublicacion />} />
                
                <Route path={"/agregarCentro"} element={<AgregarCentro />} />
                <Route path={"/Centros"} element={<ListarCentro />} />

                <Route path={"/AgregarCategoria"} element={<AgregarCategoria />} />

              </Routes>
            </div>
          </div>
        </div>
      </BrowserRouter>
    </div>
  )
}

export default App;





/*import logo from './logo.svg';
import './App.css';

function App() {
  return (
    <div className="App">
      <header className="App-header">
        <img src={logo} className="App-logo" alt="logo" />
        <p>
          Edit <code>src/App.js</code> and save to reload.
        </p>
        <a
          className="App-link"
          href="https://reactjs.org"
          target="_blank"
          rel="noopener noreferrer"
        >
          Learn React
        </a>
      </header>
    </div>
  );
}

export default App;
*/