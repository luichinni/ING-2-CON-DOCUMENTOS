import axios from 'axios';
import Publicacion from '../../components/Publicacion';
import Filtro from '../../components/Filtro';
import '../../HarryStyles/Publicaciones.css';
import { useEffect, useState } from 'react';

const ListarPublis = () => {
  const [publicaciones, setPublicaciones] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [parametros, setParametros] = useState({
    nombre: "",
    user: "",
    categoria_id: "",
    estado: "",
    id: ""
  });

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError('');

      try {
        const queryParams = new URLSearchParams(parametros).toString();
        const url = `http://localhost:8000/public/listarPublicaciones?${queryParams}&token=${localStorage.getItem('token')}`;
        const response = await axios.get(url);

        if (response.data.length === 3) {
          setError('No hay publicaciones disponibles');
          setPublicaciones([]); 
        } else {
          setPublicaciones(procesar(response.data));
        }
      } catch (error) {
        setError('Ocurrió un error al obtener las publicaciones.');
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [parametros]);

  const handleParametrosChange = (newParametros) => {
    setParametros(newParametros);
  };

  function procesar(publicaciones) {
    let publisCopy = [];
    Object.keys(publicaciones).forEach(function (clave) {
      if (!isNaN(clave)) {
        publisCopy[clave] = publicaciones[clave]
      }
    });
    return publisCopy;
  }

  return (
    <div className='content'>
      <div className='sidebar'>
        <Filtro onFiltroSubmit={handleParametrosChange} />
      </div>
      <div className='publi-container'>
        {loading ? (
          <h1 className='cargando'>Cargando...</h1>
        ) : error ? (
          <h1 className='sin-publi'>{error}</h1>
        ) : (
          publicaciones.map(publicacion => (
            <Publicacion
                  id={publicacion.id}
                  nombre={publicacion.nombre}
                  descripcion={publicacion.descripcion}
                  user={publicacion.user}
                  categoria_id={publicacion.categoria_id}
                  estado={publicacion.estado}
                  imagen={publicacion.imagenes[0].archivo}
            />
          ))
        )}
      </div>
    </div>
  );
}

export default ListarPublis;
