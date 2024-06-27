import React, { useState , useEffect } from 'react';
import "../HarryStyles/centros.css";
import "../HarryStyles/styles.css";
import axios from 'axios';

const FiltroIntercambio = ({ onFiltroSubmit }) => {
  const [centros, setCentros] = useState([]);
  const [centrosSeleccionados, setCentrosSeleccionados] = useState([]);
  const [filtro, setFiltro] = useState({
    publicacionOferta: "",
    publicacionOfertada: "",
    estado: "",
    centro: "",
    username: ""
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFiltro({
      ...filtro,
      [name]: value
    });
  };
  const handleCentrosChange = (e) => {
    const selectedValues = Array.from(e.target.selectedOptions, option => option.value);
    setCentrosSeleccionados(selectedValues);
};

  const handleSubmit = (e) => {
    e.preventDefault();
    onFiltroSubmit(filtro);
  };

  useEffect(() => {
    const fetchData = async () => {
        try {
            const res = await axios.get(`http://localhost:8000/public/listarCentros?id=&nombre=&direccion=&hora_abre=&hora_cierra=`);
            setCentros(procesarcen(res.data));
        } catch (error) {
            console.error(error);
        }
    };
    fetchData();
}, []);
function procesarcen(centros) {
  let cenCopy = [];
  Object.keys(centros).forEach(function (clave) {
      if (!isNaN(clave)) {
          cenCopy[clave] = centros[clave]
      }
  })
  return cenCopy
}

  return (
    <>
      <br/><br/><br/>
      <form onSubmit={handleSubmit} className="filtro-form">
        <label className="filtro-label">Filtrar por:</label>
        <input 
          type="text" 
          name="publicacionOferta" 
          value={filtro.publicacionOferta} 
          onChange={handleChange} 
          placeholder="Publicación Oferta"
          className="filtro-input" 
        />
        <input 
          type="text" 
          name="publicacionOfertada" 
          value={filtro.publicacionOfertada} 
          onChange={handleChange} 
          placeholder="Publicación Ofertada"
          className="filtro-input" 
        />
        {localStorage.getItem('token') !== 'tokenUser' &&
          <input 
            type="text" 
            name="username" 
            value={filtro.username} 
            onChange={handleChange} 
            placeholder="Username"
            className="filtro-input" 
          />
        }
        <select 
          name="estado" 
          value={filtro.estado} 
          onChange={handleChange} 
          className="filtro-input"
        >
          <option value="">Seleccione un Estado</option>
          <option value="pendiente">Pendiente</option>
          <option value="cancelado">Cancelado</option>
          <option value="rechazado">Rechazado</option>
          <option value="aceptado">Aceptado</option>
          <option value="concretado">Concretado</option>
        </select>
        {localStorage.getItem('token') !== 'tokenVolunt' &&
          <select id="centro" className='filtro-input' onChange={handleCentrosChange}>
            <option value="">Seleccione un centro</option>
            {centros.map((centro) => (
                <option key={centro.id} value={centro.id}>
                    {centro.nombre}
                </option>
            ))}
          </select>
        }
        <button className="filtro-button" type="submit">Filtrar</button>
      </form>
    </>
  );
};

export default FiltroIntercambio;
