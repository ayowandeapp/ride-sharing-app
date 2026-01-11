export function useDirections() {
  let directionsRenderer = null

  const drawDirections = async ({
    gMapRef,
    origin,
    destination,
  }) => {
    if (!gMapRef?.value) return

    const mapObject = await gMapRef.value.$mapPromise

    const currPoint = new google.maps.LatLng(
      origin.lat,
      origin.lng
    )

    const desPoint = new google.maps.LatLng(
      destination.lat,
      destination.lng
    )

    const directionsService = new google.maps.DirectionsService()

    // clear previous route
    if (directionsRenderer) {
      directionsRenderer.setMap(null)
    }

    directionsRenderer = new google.maps.DirectionsRenderer({
      map: mapObject,
    })

    directionsService.route(
      {
        origin: currPoint,
        destination: desPoint,
        travelMode: google.maps.TravelMode.DRIVING,
        avoidTolls: false,
        avoidHighways: false,
      },
      (res, status) => {
        if (status === google.maps.DirectionsStatus.OK) {
          directionsRenderer.setDirections(res)
        } else {
          console.error('Directions error:', status)
        }
      }
    )
  }

  return {
    drawDirections,
  }
}
