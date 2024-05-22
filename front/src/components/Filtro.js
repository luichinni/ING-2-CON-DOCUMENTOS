import React, { useState, useEffect } from 'react';
import axios from 'axios';

const Filtro = ({ onFiltroSubmit }) => {
  const [categorias, setCategorias] = useState('');
  const [filtro, setFiltro] = useState({
    nombre: "",
    user: "",
    categoria_id: "",
    estado: "",
    id: ""
  });

  const handleCategoriaChange = (e) => setCategorias(e.target.value);

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
        cateCopy[clave] = categorias[clave]
        }
      })
      return cateCopy
      }

  const handleSubmit = (e) => {
    e.preventDefault();
    onFiltroSubmit(filtro);
  };

  return (
    <form onSubmit={handleSubmit}>
      <input type="text" name="nombre" value={filtro.nombre} onChange={handleChange} placeholder="Nombre" />
      <input type="text" name="user" value={filtro.user} onChange={handleChange} placeholder="Usuario" />
      {/*+<select id="categoria" name="categoria_id" onChange={handleCategoriaChange}>
          <option value="">Categorias</option>
          {categorias.map((categoria) => (
            <option key={categoria.id} value={categoria.id}>
              {categoria.nombre}
            </option>
          ))
        </select>*/}
      <input type="text" name="estado" value={filtro.estado} onChange={handleChange} placeholder="Estado" />
      <button type="submit">Filtrar</button>
    </form>
  );
}

export default Filtro;
