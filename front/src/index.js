import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './App';
import reportWebVitals from './reportWebVitals';

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);

reportWebVitals();



/*import React from 'react'
import ReactDom from 'react-dom/client'
import { Header } from './componets/Header.js'
import { ListarPublis } from './pages/ListarPublis.js'

const root = ReactDom.createRoot(document.getElementById('root'))
root.render(inicio())
function inicio() {
    return <>
    <Header />
    <ListarPublis />
    </>
}*/