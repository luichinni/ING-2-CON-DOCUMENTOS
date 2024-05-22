import React, { useState } from 'react';

const Filtro = ({ onFiltroSubmit }) => {
  const [filtro, setFiltro] = useState({
    nombre: "",
    username: "",
    categoria_id: "",
    estado: "",
    id: ""
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFiltro({
      ...filtro,
      [name]: value
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onFiltroSubmit(filtro);
  };

  return (
    <form onSubmit={handleSubmit}>
      <input type="text" name="nombre" value={filtro.nombre} onChange={handleChange} placeholder="Nombre" />
      <input type="text" name="username" value={localStorage.getItem('nombre')} placeholder="Usuario" />
      <input type="text" name="categoria_id" value={filtro.categoria_id} onChange={handleChange} placeholder="Categoría_id" />
      <input type="text" name="estado" value={filtro.estado} onChange={handleChange} placeholder="Estado" />
      <button type="submit">Filtrar</button>
    </form>
  );
}

export default Filtro;
