import axios from 'axios';
import Publicacion from '../../components/Publicacion';
import Filtro from '../../components/Filtro';
import '../../HarryStyles/Publicaciones.css';
import { useEffect, useState } from 'react';
import Intercambio from '../../components/Intercambio';

const ListarIntercambios = () => {
  const [intercambios, setIntercambios] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const username = localStorage.getItem('username');
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
        const queryParams = new URLSearchParams({
          nombre: parametros.nombre,
          user: username,
          categoria_id: parametros.categoria_id,
          estado: parametros.estado,
          id: parametros.id
        }).toString();

        // Actualizar la URL del endpoint para listar intercambios
        const url = `http://localhost:8000/public/listarIntercambios?${queryParams}`;
        const response = await axios.get(url);

        // Manejo de la respuesta según la nueva estructura de la API
        if (response.data.Mensaje === 'No hay intercambios disponibles') {
          setError('No hay intercambios disponibles');
          setIntercambios([]);
        } else {
          setIntercambios(procesar(response.data));
          console.log("datos de intercambios:")
          console.log(intercambios)
        }
      } catch (error) {
        setError('Error al obtener intercambios.');
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

  function procesar(inter) {
    let intercambiosCopy = [];
    Object.keys(inter).forEach(function (clave) {
      if (!isNaN(clave)) {
        intercambiosCopy[clave] = inter[clave];
      }
    });
    console.log("intercambiosCopy:")
    console.log(intercambiosCopy)
    return intercambiosCopy;
  }

  return (
    <div className='content'>
      <br/><br/><br/><br/>
      <div className='sidebar'>
        <Filtro onFiltroSubmit={handleParametrosChange} />
      </div>
      <div className='publi-container'>
      <br/><br/>
        {loading ? (
          <h1 className='cargando'>Cargando...</h1>
        ) : error ? (
          <h1 className='sin-publi'>{error}</h1>
        ) : (
          intercambios.map((intercambio) => (
            <Intercambio
              key={intercambio.id} // Asegurarse de que 'key' sea único
              id={intercambio.id}
              voluntario={intercambio.voluntario}
              publicacion1={intercambio.publicacion1}
              publicacion2={intercambio.publicacion2}
              horario={intercambio.horario}
              estado={intercambio.estado}
              descripcion={intercambio.descripcion}
              donacion={intercambio.donacion}
              centro={intercambio.centro}
              fecha_propuesta={intercambio.fecha_propuesta}
            />
          ))
        )}
      </div>
    </div>
  );
}

export default ListarIntercambios;