import './App.css';
import React from "react";
import ReactDom from "react-dom";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import NavBar from "./components/Navbar";
import Header from './components/Header'; // Cambiado de { Header } a Header
import { ListarPublis } from './pages/publicaciones/ListarPublis';
import { AgregarCategoria } from './pages/categorias/AgregarCategoria';

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