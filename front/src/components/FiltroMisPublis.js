import React, { useState, useEffect } from 'react';
import axios from 'axios';
import '../HarryStyles/Filtro.css';

const FiltroMisPublis = ({ onFiltroSubmit }) => {
  const [categorias, setCategorias] = useState([]);
  const [filtro, setFiltro] = useState({
    nombre: "",
    user: "",
    categoria_id: "",
    id: ""
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFiltro({
      ...filtro,
      [name]: value
    });
  };

  useEffect(() => {
    const fetchData = async () => {
      try {
        const respon = await axios.get(`http://localhost:8000/public/listarCategorias?id=&nombre=`);
        setCategorias(procesarcat(respon.data));
        console.log(respon.data);
      } catch (error) {
        console.error(error);
      }
    };
    fetchData();
  }, []);

  function procesarcat(categorias) {
    let cateCopy = [];
    Object.keys(categorias).forEach(function (clave) {
      if (!isNaN(clave)) {
        cateCopy[clave] = categorias[clave];
      }
    });
    return cateCopy;
  }

  const handleSubmit = (e) => {
    e.preventDefault();
    onFiltroSubmit(filtro);
  };

  return (
    <form className="filtro-form" onSubmit={handleSubmit}>
      <input
        className="filtro-input"
        type="text"
        name="nombre"
        value={filtro.nombre}
        onChange={handleChange}
        placeholder="Nombre"
      /><br/><br/>
      <select
        className="filtro-input"
        id="categoria"
        name="categoria_id"
        value={filtro.categoria_id}
        onChange={handleChange}
      >
        <option value="">Categorías</option>
        {Array.isArray(categorias) && categorias.map((categoria) => (
          <option key={categoria.id} value={categoria.id}>
            {categoria.nombre}
          </option>
        ))}
      </select><br/><br/>
      <button className="filtro-button" type="submit">Filtrar</button>
    </form>
  );
}

export default FiltroMisPublis;
