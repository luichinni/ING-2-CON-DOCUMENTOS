import axios from 'axios';
import Publicacion from '../../components/Publicacion';
import Filtro from '../../components/Filtro';
import '../../HarryStyles/Publicaciones.css';
import { useEffect, useState } from 'react';

const ListarMisPublis = () => {
  const [publicaciones, setPublicaciones] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [parametros, setParametros] = useState({
    nombre: "",
    user: "",
    categoria: "",
    estado: "",
    id: ""
  });

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError('');

      try {
        const queryParams = new URLSearchParams(parametros).toString();
        const url = `https://localhost:8000/public/listarPublicaciones?${queryParams}`;
        const response = await axios.get(url);

        if (response.data.length === 0) {
          setError('No hay publicaciones disponibles');
          setPublicaciones([]); 
        } else {
          setPublicaciones(response.data);
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

  return (
    <div className='Content'>
      <div className='Publi-Div'>
        <Filtro onFiltroSubmit={handleParametrosChange} />
        {loading ? (
          <h1 className='Cargando'>Cargando...</h1>
        ) : error ? (
          <h1 className='SinPubli'>{error}</h1>
        ) : (
          publicaciones.map(publicacion => (
            <Publicacion
              key={publicacion.id}
              nombre={publicacion.nombre}
              descripcion={publicacion.descripcion}
              user={publicacion.user}
              categoria={publicacion.categoria}
              estado={publicacion.estado}
            />
          ))
        )}
      </div>
    </div>
  );
}

export default ListarMisPublis;