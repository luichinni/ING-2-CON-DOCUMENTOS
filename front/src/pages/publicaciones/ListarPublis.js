import axios from 'axios';
import Publicacion from '../../components/PublicacionComponent';
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
          setPublicaciones([]); // Limpiar el estado de las publicaciones en caso de error
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

export default ListarPublis;



  /*const filasDePublicaciones = [];
  for (let i = 0; i < ListaDePublicaciones.length; i += 3) {
    filasDePublicaciones.push(ListaDePublicaciones.slice(i, i + 3));
  }
  
  return (
    <div className="ListadoPublis">
      {filasDePublicaciones.map((fila, index) => (
        <div key={index} className="fila">
          {fila.map(publicacion => (
            <div key={publicacion.ID} className="publicacion">
              <Tarjeta publicacion={publicacion} />
            </div>
          ))}
        </div>
      ))}
    </div>
  );
}*/