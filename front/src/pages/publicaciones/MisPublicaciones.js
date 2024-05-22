import React from 'react';
import ListarMisPublis from './ListarMisPublis';
import '../../HarryStyles/Publicaciones.css';
import '../../HarryStyles/styles.css';


const MisPublis = () => {
  return (
    <div className='dashboard'>
      <br /><br /><br /><br />
      <banner className='banner'>
        <h1>Mis publicaciones</h1>
      </banner>

      <ListarMisPublis />
    </div>
  );
}

export default MisPublis;