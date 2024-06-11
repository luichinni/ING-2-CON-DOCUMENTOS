import axios from 'axios';
import InterPubli from '../../components/InterPubli';
import FiltroInter from '../../components/FiltroPubliInter';
import '../../HarryStyles/Publicaciones.css';
import { useEffect, useState } from 'react';

const ListarPubliInter = () => {
  const [publicaciones, setPublicaciones] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const username = localStorage.getItem('username')
  const categoria = localStorage.getItem("categoriaInter")
  const [parametros, setParametros] = useState({
    nombre: "",
    user: "",
    categoria_id: "",
    estado: "",
    id: "",
    like: "false"
  });

  function categoria_id(categoria){
    const fetchData = async () => {
            function procesarcat(categorias) {
              let cateCopy = [];
              Object.keys(categorias).forEach(function (clave) {
                if (!isNaN(clave)) {
                  cateCopy[clave] = categorias[clave];
                }
              });
              return cateCopy[0].id;
            }
      try {
        let categoria
        const respon = await axios.get(`http://localhost:8000/public/listarCategorias?id=&nombre=`);
        return categoria = (procesarcat(respon.data));
      } catch (error) {
        console.error(error);
      }
    };
    fetchData();
  }
  
  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError('');

      try {
        const queryParams = new URLSearchParams({
            nombre: parametros.nombre,
            user: username,
            categoria_id: categoria_id(categoria),
            estado: parametros.estado,
            id: parametros.id,
            habilitado: 0
        }).toString();
        const url = `http://localhost:8000/public/listarPublicaciones?${queryParams}&token=${localStorage.getItem('token')}`;
        const response = await axios.get(url);

        if (response.data.length === 3) {
          setError('No tenes publicaciones de la misma categoría.');
          console.log('disponibles')
        } else {
          setPublicaciones(procesar(response.data));
        }
      } catch (error) {
        setError('No tenes publicaciones de la misma categoría.');
        console.log('encontradas')
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  },[parametros]);

  const handleParametrosChange = (newParametros) => {
    setParametros(newParametros);
  };

  function procesar(publicaciones) {
    let publisCopy = [];
    Object.keys(publicaciones).forEach(function (clave) {
      if (!isNaN(clave)) {
        publisCopy[clave] = publicaciones[clave]
      }
    })
    return publisCopy
  }

  return (
    <div className='content'>
      <div className='sidebar'>
        <FiltroInter onFiltroSubmit={handleParametrosChange} />
      </div>
      <div className='publi-container'>
        {loading ? (
          <h1 className='cargando'>Cargando...</h1>
        ) : error ? (
          <>
          <br/><br/><br/><br/>
          <h1 className='sin-publi'>{error}</h1>
          </>
        ) : (
          publicaciones.map(publicacion => (
            <InterPubli
              key={publicacion.id}//evita advertencia
              id={publicacion.id}
              nombre={publicacion.nombre}
              descripcion={publicacion.descripcion}
              user={publicacion.user}
              categoria_id={publicacion.categoria_id}
              estado={publicacion.estado}
              imagen={publicacion.imagenes[0].archivo}
            />
          )
        ))}
      </div>
    </div>
  );
}

export default ListarPubliInter;
