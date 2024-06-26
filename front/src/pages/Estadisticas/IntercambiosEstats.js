import axios from 'axios';
import Publicacion from '../../components/Publicacion';
import FiltroIntercambio from '../../components/FiltroIntercambio';
import '../../HarryStyles/Publicaciones.css';
import { useEffect, useState } from 'react';
import Estadistica from '../../components/Estadistica';

const IntercambiosEstats = () => {
  const [intercambios, setIntercambios] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const username = localStorage.getItem('username');
  const token = localStorage.getItem('token');
  const [parametros, setParametros] = useState({
    publicacionOferta: "",
    publicacionOfertada: "",
    estado: "",
    centro: "",
    username: ""
  });

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError('');

      try {
        let centro = "";
        if (token === 'tokenVolunt') {
          centro = await obtenerCentroUsuario(username);
        }

        const queryParams = new URLSearchParams({
          publicacionOferta: parametros.publicacionOferta,
          publicacionOfertada: parametros.publicacionOfertada,
          estado: parametros.estado,
          centro: parametros.centro,
          username: parametros.username,
        }).toString();

        console.log(`params: ${queryParams}`)


        const url = `http://localhost:8000/public/listarIntercambios?${queryParams}&token=${localStorage.getItem('token')}`;
        console.log(`mandar: ${url}`)
        const response = await axios.get(url);

        if (response.data.Mensaje === 'No hay intercambios disponibles') {
          setError(`¡No has realizado intercambios todavía! \n Ve a explorar para poder intercambiar`);
          setIntercambios([]);
        } else {
          let intercambiosList = procesar(response.data);
          setIntercambios(intercambiosList);
        }
      } catch (error) {
        setError(`¡No has realizado intercambios todavía! \n Ve a explorar para poder intercambiar`);
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [parametros, username, token]);

  const obtenerCentroUsuario = async (volun) => {
    try {
      const url = `http://localhost:8000/public/obtenerCentroVolun?voluntario=${volun}`;
      const response = await axios.get(url);
      const centroId = response.data[0]?.centro ?? "";
      console.log(centroId)
      return centroId;
    } catch (error) {
      setError(`No puedes ver los intercambios disponibles \n porque no estás asociado a ningún centro`);
      console.error(error);
      return "";
    }
  };

  const handleParametrosChange = async (newParametros) => {
    if (token === 'tokenVolunt') {
      const centro = await obtenerCentroUsuario(username);
      setParametros({ ...newParametros, centro });
    } else {
      setParametros(newParametros);
    }
  };

  function procesar(inter) {
    // Convertir la respuesta en una lista única de intercambios
    const intercambiosCopy = [];
    const seenIds = new Set();

    Object.keys(inter).forEach((clave) => {
      if (!isNaN(clave)) {
        const intercambio = inter[clave];
        if (!seenIds.has(intercambio.id)) {
          seenIds.add(intercambio.id);
          intercambiosCopy.push(intercambio);
        }
      }
    });

    return intercambiosCopy;
  }

  return (
    <div className='content'>
      <div className='sidebar'>
        <FiltroIntercambio onFiltroSubmit={handleParametrosChange} />
      </div>
      <div className='publi-container'>
        {loading ? (
          <h1 className='cargando'>Cargando...</h1>
        ) : error ? (
          <>
            <br /><br /><br />
            <h1 className='sin-publi'>{error}</h1>
          </>
        ) : (
          intercambios.map((intercambio) => (
            <Estadistica
              key={intercambio.id}
              id={intercambio.id}
              voluntario={intercambio.voluntario}
              publicacionOferta={intercambio.publicacionOferta}
              publicacionOfertada={intercambio.publicacionOfertada}
              ofertaAcepta={intercambio.ofertaAcepta}
              ofertadaAcepta={intercambio.ofertadaAcepta}
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

export default IntercambiosEstats;
