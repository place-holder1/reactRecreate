import styles from "../styles/songPlayer.module.css";
import SpotifyWebPlayer from "react-spotify-web-playback";
import React, { useState, useEffect } from "react";

export default function SongPlayer({ accessToken, trackUri }) {
    const [play, setPlay] = useState(false)
  
    useEffect(() => setPlay(true), [trackUri])
  
    if (!accessToken) return null
    return (
      <SpotifyWebPlayer
        token={accessToken}
        showSaveIcon
        callback={state => {
          if (!state.isPlaying) setPlay(false)
        }}
        play={play}
        uris={trackUri ? [trackUri] : []}
      />
    )
  }