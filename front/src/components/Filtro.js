import React, { useState } from 'react';

const Filtro = ({ onFiltroSubmit }) => {
  const [filtro, setFiltro] = useState({
    nombre: "",
    user: "",
    categoria: "",
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
      <input type="text" name="user" value={filtro.user} onChange={handleChange} placeholder="Usuario" />
      <input type="text" name="categoria" value={filtro.categoria} onChange={handleChange} placeholder="Categoría" />
      <input type="text" name="estado" value={filtro.estado} onChange={handleChange} placeholder="Estado" />
      <button type="submit">Filtrar</button>
    </form>
  );
}

export default Filtro;
