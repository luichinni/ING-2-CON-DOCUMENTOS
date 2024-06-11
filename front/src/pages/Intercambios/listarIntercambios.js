import axios from 'axios';
import Publicacion from '../../components/Publicacion';
import FiltroIntercambio from '../../components/FiltroIntercambio';
import '../../HarryStyles/Publicaciones.css';
import { useEffect, useState } from 'react';
import Intercambio from '../../components/Intercambio';

const ListarIntercambios = () => {
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
    horario:""
    });

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError('');

      try {
        const queryParams = new URLSearchParams({
          publicacionOferta: parametros.publicacionOferta,
          publicacionOfertada: parametros.publicacionOfertada,
          estado: parametros.estado,
          centro: parametros.centro,
          horario: parametros.horario
        }).toString();

        const url = `http://localhost:8000/public/listarIntercambios?${queryParams}`;
        const response = await axios.get(url);
        
        if (response.data.Mensaje === 'No hay intercambios disponibles') {
          setError(`¡No has realizado intercambios todavía! \n Ve a explorar para poder intercambiar`);
          setIntercambios([]);
        } else {
          setIntercambios(procesar(response.data));
        }
      } catch (error) {
        setError(`¡No has realizado intercambios todavía! \n Ve a explorar para poder intercambiar`);
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [parametros]);

  const centroUsuario = async (volun) => {
    try {
      const url = `http://localhost:8000/public/obtenerCentroVolun?voluntario=${volun}`;
      const response = await axios.get(url);
      return response;
    } catch (error) {
      setError(`No podes ver los intercambios disponibles \n porque no estas asociado a ningun centro`);
      console.error(error);
      return error;
    }
}

const handleParametrosChange = async (newParametros) => {
  if (token === 'tokenVolunt') {
    const centro = await centroUsuario(username);
    setParametros({ ...newParametros, centro: centro.data.centro });
  } else {
    setParametros(newParametros);
  }
}

  function procesar(inter) {
    let intercambiosCopy = [];
    Object.keys(inter).forEach(function (clave) {
      if (!isNaN(clave)) {
        intercambiosCopy[clave] = inter[clave];
      }
    });
    console.log(intercambiosCopy)
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
            <br/><br/><br/>
            <h1 className='sin-publi'>{error}</h1>
          </>
        ) : (
          intercambios.map((intercambio) => (
            <Intercambio
              key={intercambio.id}
              id={intercambio.id}
              voluntario={intercambio.voluntario}
              publicacionOferta={intercambio.publicacionOferta}
              publicacionOfertada={intercambio.publicacionOfertada}
              ofertaAcepta={intercambio.ofertaAcepta}
              ofertaAceptada={intercambio.ofertaAceptada}
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