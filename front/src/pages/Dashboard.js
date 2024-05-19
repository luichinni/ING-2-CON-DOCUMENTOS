import axios from 'axios';
import Publicacion from '../components/PublicacionComponent';
import Filtro from '../components/Filtro';
import '../HarryStyles/Publicaciones.css';
import { useEffect, useState } from 'react';
import { ListaDePublicaciones } from './publicaciones/ListaDePublicaciones';
import Tarjeta from './publicaciones/Tarjeta'

const ListarPublis = () => {
  const [Publi, setPubli] = useState([]);
  const [error, setError] = useState('');
  const [parametros, setParametros] = useState({
    nombre: "",
    user: "",
    categoria: "",
    estado: "",
    id: ""
  });

  useEffect(() => {
    const fetchData = async () => {
      try {
        const queryParams = new URLSearchParams(parametros);
        const url = `https://localhost:8000/public/listarPublicaciones?${queryParams.toString()}`;
        const response = await axios.get(url);

        if (response.data.length === 0) {
          setError('No hay publicaciones disponibles');
          setPubli([]); // Limpiar el estado de las publicaciones en caso de error
        } else {
          setPubli(response.data);
          setError('');
        }

        console.log(response.data);
      } catch (error) {
        setError('Ocurrió un error al obtener las publicaciones.');
        console.log(error);
      }
    };

    fetchData();
  }, [parametros]);

  const handleParametrosChange = (newParametros) => {
    setParametros(newParametros);
  };

 /* return (
    <div className='Content'>
      <div className='Publi-Div'>
        <Filtro onFiltroSubmit={handleParametrosChange} />
        {error ? (
          <h1 className='SinPubli'>{error}</h1>
        ) : (
          Publi.map(juego => (
            <Publicacion
              key={juego.id}
              nombre={juego.nombre}
              descripcion={juego.descripcion}
              user={juego.user}
              categoria={juego.categoria}
              estado={juego.estado}
            />
          ))
        )}
      </div>
    </div>
  );
}

export default ListarPublis;*/



/*import Tarjeta from './publicaciones/Tarjeta';
import { ListaDePublicaciones } from './publicaciones/ListaDePublicaciones';
import '../HarryStyles/Publicaciones.css';

const ListarPublis = () => {
  
  if (ListaDePublicaciones.length === 0){
    return <h1 className="SinPubli">¡No hay publicaciones disponibles en este momento!</h1>;
  }*/
  
  const filasDePublicaciones = [];
  for (let i = 0; i < ListaDePublicaciones.length; i += 3) {
    filasDePublicaciones.push(ListaDePublicaciones.slice(i, i + 3));
  }
  
  return (
    <div className="Contents">
      {ListaDePublicaciones.map(publicacion => (
        <div key={publicacion.ID} className="Publi-Div">
          <Tarjeta publicacion={publicacion} />
        </div>
      ))}
    </div>
  );
  
  
}

export default ListarPublis;
