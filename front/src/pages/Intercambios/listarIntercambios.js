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
    horario: ""
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
          centro: token === 'tokenVolunt' ? centro : parametros.centro,
          horario: parametros.horario
        }).toString();

        const url = `http://localhost:8000/public/listarIntercambios?${queryParams}`;
        const response = await axios.get(url);

        if (response.data.Mensaje === 'No hay intercambios disponibles') {
          setError(`¡No has realizado intercambios todavía! \n Ve a explorar para poder intercambiar`);
          setIntercambios([]);
        } else {
          let intercambiosList = procesar(response.data);
          if (token === 'tokenUser') {
            intercambiosList = await Mispublicaciones(intercambiosList);
          }
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
  }, [parametros, username, token]); // Añadir `token` a la dependencia

  const obtenerCentroUsuario = async (volun) => {
    try {
      const url = `http://localhost:8000/public/obtenerCentroVolun?voluntario=${volun}`;
      const response = await axios.get(url);
      const centroId = response.data[0]?.centro ?? ""; // Acceder al centro ID desde la respuesta
      return centroId;
    } catch (error) {
      setError(`No puedes ver los intercambios disponibles \n porque no estás asociado a ningún centro`);
      console.error(error);
      return ""; // Devuelve un valor por defecto en caso de error
    }
  };

  const Mispublicaciones = async (intercambios) => {
    try {
      const userPublicacionesIds = [];

      for (const intercambio of intercambios) {
        const ids = [intercambio.publicacionOferta, intercambio.publicacionOfertada];
        for (const id of ids) {
          const url = `http://localhost:8000/public/listarPublicaciones?id=${id}&token=${localStorage.getItem('token')}`;
          const response = await axios.get(url);
          const publicacion = response.data[0];
          if (publicacion.user === username) {
            userPublicacionesIds.push(publicacion.id);
          }
        }
      }

      const intercambiosFiltrados = intercambios.filter(
        intercambio => userPublicacionesIds.includes(intercambio.publicacionOferta) || userPublicacionesIds.includes(intercambio.publicacionOfertada)
      );

      return intercambiosFiltrados;
    } catch (error) {
      setError(`Error al filtrar tus intercambios`);
      console.error(error);
      return [];
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
    let intercambiosCopy = [];
    Object.keys(inter).forEach(function (clave) {
      if (!isNaN(clave)) {
        intercambiosCopy[clave] = inter[clave];
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
